<?php
/** app/Controllers/SessionController.php  -  meetings: online, physical or hybrid */
class SessionController extends Controller
{
    public function index(): void
    {
        $mode = (string) Request::query("mode");
        $filters = [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "mode" => isset(config("app.session_modes")[$mode]) ? $mode : "",
            "mine" => Request::query("mine") === "1",
            "when" => Request::query("when") === "past" ? "past" : "",
            "faculty" => Auth::facultyScope(),
        ];
        $this->view("sessions/index", [
            "title" => "Meetings | Ncircle",
            "pageCss" => "groups",
            "filters" => $filters,
            "result" => StudySession::search($filters, Auth::id(), $this->page()),
            "page" => $this->page(),
        ], "app");
    }

    public function create(): void
    {
        $group = $this->memberGroup((int) Request::query("group"));
        $this->view("sessions/create", [
            "title" => "Schedule a meeting | Ncircle",
            "pageCss" => "groups",
            "subjects" => Subject::options(Auth::facultyScope()),
            "group" => $group,
            "meeting" => null,
        ], "app");
    }

    public function store(): void
    {
        $group = null;
        if ((int) Request::input("group_id") > 0) {
            $group = $this->memberGroup((int) Request::input("group_id"));
            if ($group === null) {
                Response::error(403);
            }
        }
        $data = $this->validatedMeeting($group);
        $data["group_id"] = $group["id"] ?? null;
        $id = StudySession::create(Auth::id(), $data);

        if ($group !== null) {
            $people = StudySession::peopleToNotify(["id" => $id, "group_id" => $group["id"]]);
            NotificationService::meetingScheduled(["group_id" => $group["id"], "title" => $data["title"]], $people, Auth::id(), $group["name"]);
        }
        Flash::add("success", "Your meeting is scheduled.");
        Response::redirect($group !== null ? "/groups/" . (int) $group["id"] : "/sessions");
    }

    public function edit(int $id): void
    {
        $meeting = $this->loadManaged($id);
        $this->view("sessions/create", [
            "title" => "Edit meeting | Ncircle",
            "pageCss" => "groups",
            "subjects" => Subject::options(Auth::facultyScope()),
            "group" => !empty($meeting["group_id"]) ? Group::find((int) $meeting["group_id"]) : null,
            "meeting" => $meeting,
        ], "app");
    }

    public function update(int $id): void
    {
        $meeting = $this->loadManaged($id);
        $group = !empty($meeting["group_id"]) ? Group::find((int) $meeting["group_id"]) : null;
        $data = $this->validatedMeeting($group);
        if ((int) $data["capacity"] < StudySession::goingCount($id)) {
            $this->failValidation(["capacity" => "More people are already going than that."]);
        }
        StudySession::update($id, $data);
        NotificationService::meetingChanged(array_merge($meeting, $data), StudySession::peopleToNotify($meeting), Auth::id());
        Flash::add("success", "Meeting updated.");
        Response::redirect($group !== null ? "/groups/" . (int) $group["id"] : "/sessions");
    }

    public function join(int $id): void
    {
        $user = Auth::user();
        $session = $this->loadVisible($id);
        if (strtotime($session["starts_at"]) < time()) {
            Flash::add("warning", "This meeting has already started.");
        } elseif (StudySession::join($id, (int) $user["id"])) {
            NotificationService::sessionJoined($session, (int) $user["id"], $user["name"]);
            Flash::add("success", "You're going to " . $session["title"] . ".");
        } else {
            Flash::add("warning", "This meeting is full.");
        }
        Response::back("/sessions");
    }

    public function leave(int $id): void
    {
        $session = $this->loadVisible($id);
        if ((int) $session["host_id"] === Auth::id()) {
            Flash::add("warning", "You are hosting this meeting. Cancel it instead.");
        } elseif (strtotime($session["starts_at"]) < time()) {
            Flash::add("warning", "This meeting has already started.");
        } else {
            StudySession::decline($id, Auth::id());
            Flash::add("success", "We've noted that you can't go.");
        }
        Response::back("/sessions");
    }

    public function destroy(int $id): void
    {
        $session = $this->orNotFound(StudySession::find($id));
        if (!StudySession::canManage($session, Auth::user())) {
            Response::error(StudySession::canSee($session, Auth::user()) ? 403 : 404);
        }
        $people = StudySession::peopleToNotify($session);
        StudySession::delete($id);
        NotificationService::sessionCancelled($session, $people, Auth::id());
        Flash::add("success", "Meeting cancelled.");
        Response::redirect(!empty($session["group_id"]) ? "/groups/" . (int) $session["group_id"] : "/sessions");
    }

    private function loadVisible(int $id): array
    {
        $session = $this->orNotFound(StudySession::find($id));
        if (!StudySession::canSee($session, Auth::user())) {
            Response::error(404);
        }
        return $session;
    }

    /** Hidden meetings stay a 404, so a 403 never confirms they exist. Past meetings cannot be edited. */
    private function loadManaged(int $id): array
    {
        $session = $this->loadVisible($id);
        if (!StudySession::canManage($session, Auth::user())) {
            Response::error(403);
        }
        if (strtotime($session["starts_at"]) < time()) {
            Flash::add("warning", "Past meetings cannot be edited.");
            Response::redirect("/sessions");
        }
        return $session;
    }

    private function memberGroup(int $groupId): ?array
    {
        if ($groupId <= 0 || !Group::isMember($groupId, Auth::id())) {
            return null;
        }
        return Group::find($groupId);
    }

    /** Checks the form and returns the values the model needs. Group meetings use the group's subject. */
    private function validatedMeeting(?array $group): array
    {
        $modes = implode(",", array_keys(config("app.session_modes")));
        $rules = [
            "title" => "required|min:5|max:160",
            "mode" => "required|in:{$modes}",
            "date" => "required|date",
            "start_time" => "required",
            "end_time" => "required",
            "capacity" => "required|between:2,200",
            "description" => "required|min:10|max:500",
            "location" => "max:160",
            "meeting_url" => "max:500",
        ];
        if ($group === null) {
            $rules["subject_id"] = "required|int";
        }
        $data = $this->validate($rules, ["subject_id" => "Subject", "mode" => "meeting type", "date" => "Date", "start_time" => "Start time", "end_time" => "End time", "capacity" => "Seats", "description" => "Details", "meeting_url" => "Meeting link"]);

        $errors = [];
        if ($group === null && !Subject::isActive((int) $data["subject_id"], Auth::facultyScope())) {
            $errors["subject_id"] = "Choose a valid subject.";
        }
        $start = strtotime($data["date"] . " " . $data["start_time"]);
        $end = strtotime($data["date"] . " " . $data["end_time"]);
        if ($start === false || $start <= time()) {
            $errors["start_time"] = "The meeting must start in the future.";
        }
        if ($end === false || ($start !== false && $end <= $start)) {
            $errors["end_time"] = "The end time must be after the start time.";
        }
        $location = trim((string) ($data["location"] ?? ""));
        $link = trim((string) ($data["meeting_url"] ?? ""));
        if (in_array($data["mode"], ["physical", "hybrid"], true) && $location === "") {
            $errors["location"] = "Add the building and room.";
        }
        if (in_array($data["mode"], ["online", "hybrid"], true)) {
            if ($link === "") {
                $errors["meeting_url"] = "Add the meeting link.";
            } elseif (!filter_var($link, FILTER_VALIDATE_URL) || !str_starts_with(strtolower($link), "https://")) {
                $errors["meeting_url"] = "Use a full link starting with https://";
            }
        }
        if ($errors) {
            $this->failValidation($errors, $data);
        }
        return [
            "subject_id" => $group !== null ? (int) $group["subject_id"] : (int) $data["subject_id"],
            "title" => $data["title"],
            "description" => $data["description"],
            "mode" => $data["mode"],
            "starts_at" => date("Y-m-d H:i:s", $start),
            "ends_at" => date("Y-m-d H:i:s", $end),
            "location" => $data["mode"] === "online" ? null : $location,
            "meeting_url" => $data["mode"] === "physical" ? null : $link,
            "capacity" => (int) $data["capacity"],
        ];
    }
}
