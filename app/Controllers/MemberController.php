<?php
/**
 * app/Controllers/MemberController.php  -  Find Members: requests and the directory, across all faculties.
 * An email is only ever shown between the two people on an accepted response.
 */
class MemberController extends Controller
{
    public function requests(): void
    {
        $type = (string) Request::query("type");
        $faculty = (string) Request::query("faculty");
        $filters = [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "type" => isset(config("app.help_types")[$type]) ? $type : "",
            "faculty" => isset(config("app.faculties")[$faculty]) ? $faculty : "",
            "mine" => Request::query("mine") === "1",
        ];
        $this->view("members/index", [
            "title" => "Find Members | Ncircle",
            "pageCss" => "members",
            "filters" => $filters,
            "result" => HelpRequest::search($filters, Auth::id(), $this->page()),
            "page" => $this->page(),
        ], "app");
    }

    public function directory(): void
    {
        $faculty = (string) Request::query("faculty");
        $filters = [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "faculty" => isset(config("app.faculties")[$faculty]) ? $faculty : "",
            "open" => Request::query("open") === "1",
        ];
        $this->view("members/directory", [
            "title" => "Member directory | Ncircle",
            "pageCss" => "members",
            "filters" => $filters,
            "result" => User::directory($filters, $this->page()),
            "page" => $this->page(),
        ], "app");
    }

    public function create(): void
    {
        $this->view("members/create", ["title" => "Post a request | Ncircle", "pageCss" => "members"], "app");
    }

    public function store(): void
    {
        $types = implode(",", array_keys(config("app.help_types")));
        $data = $this->validate([
            "title" => "required|min:10|max:160",
            "type" => "required|in:{$types}",
            "description" => "required|min:20|max:2000",
            "skills" => "max:255",
        ], ["type" => "request type", "description" => "Details", "skills" => "Skills"]);

        $picked = $_POST["faculties"] ?? [];
        $faculties = is_array($picked) ? array_values(array_intersect(array_keys(config("app.faculties")), $picked)) : [];
        $id = HelpRequest::create(Auth::id(), $data, $faculties);
        Flash::add("success", "Your request is live.");
        Response::redirect("/members/requests/" . $id);
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $request = $this->loadVisible($id);
        $isOwner = (int) $request["user_id"] === (int) $user["id"];
        $mine = $isOwner ? null : HelpRequest::responseBy($id, (int) $user["id"]);

        $responses = [];
        if ($isOwner || $user["role"] === "admin") {
            $responses = HelpRequest::responses($id);
            foreach ($responses as &$response) {
                // Admins moderate content, so they do not get this contact route.
                $response["email"] = $isOwner && $response["status"] === "accepted" ? HelpRequest::contactEmail((int) $response["user_id"]) : null;
            }
            unset($response);
        }

        $this->view("members/show", [
            "title" => $request["title"] . " | Ncircle",
            "pageCss" => "members",
            "request" => $request,
            "isOwner" => $isOwner,
            "myResponse" => $mine,
            "ownerEmail" => ($mine["status"] ?? "") === "accepted" ? HelpRequest::contactEmail((int) $request["user_id"]) : null,
            "responses" => $responses,
        ], "app");
    }

    public function respond(int $id): void
    {
        $user = Auth::user();
        $request = $this->loadVisible($id);
        if ((int) $request["user_id"] === (int) $user["id"]) {
            Response::error(403);
        }
        if ($request["status"] !== "open") {
            Flash::add("warning", "This request is closed.");
            Response::redirect("/members/requests/" . $id);
        }
        if (HelpRequest::responseBy($id, (int) $user["id"]) !== null) {
            Flash::add("info", "You've already responded to this request.");
            Response::redirect("/members/requests/" . $id);
        }
        $data = $this->validate(["message" => "required|min:10|max:500"], ["message" => "Message"]);
        HelpRequest::respond($id, (int) $user["id"], $data["message"]);
        NotificationService::helpInterest($request, $user["name"]);
        Flash::add("success", "Sent. You'll see their email here if they accept.");
        Response::redirect("/members/requests/" . $id);
    }

    public function close(int $id): void
    {
        $request = $this->loadVisible($id);
        if ((int) $request["user_id"] !== Auth::id()) {
            Response::error(403);
        }
        HelpRequest::close($id);
        Flash::add("success", "Request closed.");
        Response::redirect("/members/requests/" . $id);
    }

    public function accept(int $id): void
    {
        [$response, $request] = $this->loadOwnResponse($id);
        if (HelpRequest::decide($id, "accepted")) {
            NotificationService::helpAccepted($request, (int) $response["user_id"]);
            Flash::add("success", "Accepted. You can now see each other's email.");
        }
        Response::redirect("/members/requests/" . $request["id"]);
    }

    public function decline(int $id): void
    {
        [$response, $request] = $this->loadOwnResponse($id);
        if (HelpRequest::decide($id, "declined")) {
            NotificationService::helpDeclined($request, (int) $response["user_id"]);
            Flash::add("success", "Response declined.");
        }
        Response::redirect("/members/requests/" . $request["id"]);
    }

    /** Only the person who posted the request can answer its responses. */
    private function loadOwnResponse(int $id): array
    {
        $response = $this->orNotFound(HelpRequest::findResponse($id));
        $request = $this->orNotFound(HelpRequest::find((int) $response["request_id"]));
        if ((int) $request["user_id"] !== Auth::id()) {
            Response::error(403);
        }
        return [$response, $request];
    }

    private function loadVisible(int $id): array
    {
        $request = $this->orNotFound(HelpRequest::find($id));
        $user = Auth::user();
        if ($request["is_hidden"] && $user["role"] !== "admin" && (int) $request["user_id"] !== (int) $user["id"]) {
            Response::error(404);
        }
        return $request;
    }
}
