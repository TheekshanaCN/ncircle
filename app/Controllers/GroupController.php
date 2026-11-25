<?php
/** app/Controllers/GroupController.php  -  study groups */
class GroupController extends Controller
{
    /** The group, or a 404 when it belongs to another faculty. Its owner can always see it. */
    private function loadVisible(int $id): array
    {
        $group = $this->orNotFound(Group::find($id));
        if ((int) $group["owner_id"] !== Auth::id() && !Auth::canSeeFaculty($group["subject_faculty"])) {
            Response::error(404);
        }
        return $group;
    }

    public function index(): void
    {
        $filters = [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "subject" => (int) Request::query("subject"),
            "faculty" => Auth::facultyScope(),
            "mine" => Request::query("mine") === "1",
        ];
        $this->view("groups/index", [
            "title" => "Study groups | Ncircle",
            "pageCss" => "groups",
            "filters" => $filters,
            "result" => Group::search($filters, Auth::id(), $this->page()),
            "page" => $this->page(),
            "subjects" => Subject::options(Auth::facultyScope()),
        ], "app");
    }

    public function create(): void
    {
        $this->view("groups/create", [
            "title" => "Create a group | Ncircle",
            "pageCss" => "groups",
            "subjects" => Subject::options(Auth::facultyScope()),
            "group" => null,
        ], "app");
    }

    public function edit(int $id): void
    {
        $this->view("groups/create", [
            "title" => "Edit group | Ncircle",
            "pageCss" => "groups",
            "subjects" => [],
            "group" => $this->loadOwn($id),
        ], "app");
    }

    /** The subject stays fixed, because the group's meetings belong to it too. */
    public function update(int $id): void
    {
        $this->loadOwn($id);
        $data = $this->validate([
            "name" => "required|min:3|max:100",
            "description" => "required|min:10|max:500",
            "max_members" => "required|between:2,100",
        ], ["max_members" => "Member limit"]);
        if (!Group::update($id, $data)) {
            $this->failValidation(["max_members" => "The group already has more members than that."], $data);
        }
        Flash::add("success", "Group updated.");
        Response::redirect("/groups/" . $id);
    }

    private function loadOwn(int $id): array
    {
        $group = $this->loadVisible($id);
        if ((int) $group["owner_id"] !== Auth::id()) {
            Response::error(403);
        }
        return $group;
    }

    public function store(): void
    {
        $data = $this->validate([
            "name" => "required|min:3|max:100",
            "subject_id" => "required|int",
            "description" => "required|min:10|max:500",
            "max_members" => "required|between:2,100",
        ], ["subject_id" => "Subject", "max_members" => "Member limit"]);
        if (!Subject::isActive((int) $data["subject_id"], Auth::facultyScope())) {
            $this->failValidation(["subject_id" => "Choose a valid subject."], $data);
        }
        $id = Group::create(Auth::id(), $data);
        Flash::add("success", "Your group is ready.");
        Response::redirect("/groups/" . $id);
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $group = $this->loadVisible($id);
        $isMember = Group::isMember($id, (int) $user["id"]);
        $this->view("groups/show", [
            "title" => $group["name"] . " | Ncircle",
            "pageCss" => "groups",
            "group" => $group,
            "isMember" => $isMember,
            "isOwner" => (int) $group["owner_id"] === (int) $user["id"],
            "isAdmin" => $user["role"] === "admin",
            "members" => Group::members($id),
            "posts" => $isMember ? Attachment::attachTo(Group::posts($id), "group_post") : [],
            "upcoming" => StudySession::forGroup($id, (int) $user["id"]),
            "past" => $isMember ? StudySession::forGroup($id, (int) $user["id"], true) : [],
        ], "app");
    }

    public function join(int $id): void
    {
        $user = Auth::user();
        $group = $this->loadVisible($id);
        if (Group::isMember($id, (int) $user["id"])) {
            Response::redirect("/groups/" . $id);
        }
        if (Group::join($id, (int) $user["id"])) {
            NotificationService::groupJoined($group, (int) $user["id"], $user["name"]);
            Flash::add("success", "You joined " . $group["name"] . ".");
        } else {
            Flash::add("warning", "This group is full.");
        }
        Response::redirect("/groups/" . $id);
    }

    public function leave(int $id): void
    {
        $group = $this->orNotFound(Group::find($id));
        if ((int) $group["owner_id"] === Auth::id()) {
            Flash::add("warning", "You own this group. Delete it instead of leaving.");
            Response::redirect("/groups/" . $id);
        }
        Group::leave($id, Auth::id());
        StudySession::removeFromGroupMeetings($id, Auth::id());
        Flash::add("success", "You left " . $group["name"] . ".");
        Response::redirect("/groups");
    }

    public function destroy(int $id): void
    {
        $user = Auth::user();
        $group = $this->orNotFound(Group::find($id));
        if ((int) $group["owner_id"] !== (int) $user["id"] && $user["role"] !== "admin") {
            Response::error(403);
        }
        Group::delete($id);
        Flash::add("success", "Group deleted.");
        Response::redirect("/groups");
    }

    public function post(int $id): void
    {
        $this->loadVisible($id);
        if (!Group::isMember($id, Auth::id())) {
            Response::error(403);
        }
        $data = $this->validate(["body" => "max:1000"], ["body" => "Message"]);
        $media = AttachmentService::prepare($_FILES["attachments"] ?? null, (string) Request::input("gif_id"));
        if (isset($media["error"])) {
            $this->failValidation(["attachments" => $media["error"]], $data);
        }
        if (($data["body"] ?? "") === "" && !AttachmentService::hasContent($media)) {
            $this->failValidation(["body" => "Write a message, or add an image, file or GIF."], $data);
        }
        $postId = Group::addPost($id, Auth::id(), $data["body"] ?? "");
        AttachmentService::save("group_post", $postId, Auth::id(), $media);
        Response::redirect("/groups/" . $id . "#wall");
    }

    /** The author, the group owner or an admin can delete a post. */
    public function deletePost(int $id): void
    {
        $post = $this->orNotFound(Group::findPost($id));
        $group = $this->loadVisible((int) $post["group_id"]);
        if ((int) $post["user_id"] !== Auth::id() && (int) $group["owner_id"] !== Auth::id() && !Auth::isAdmin()) {
            Response::error(403);
        }
        Group::deletePost($id);
        Flash::add("success", "Post deleted.");
        Response::redirect("/groups/" . $group["id"] . "#wall");
    }
}
