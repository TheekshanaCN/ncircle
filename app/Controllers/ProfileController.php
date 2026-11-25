<?php
/** app/Controllers/ProfileController.php  -  profiles, settings and the personal study space */
class ProfileController extends Controller
{
    public function me(): void
    {
        Response::redirect("/profile/" . Auth::id());
    }

    public function show(int $id): void
    {
        $profile = $this->orNotFound(User::find($id));
        $this->view("profile/show", [
            "title" => $profile["name"] . " | Ncircle",
            "pageCss" => "profile",
            "profile" => $profile,
            "isMe" => $id === Auth::id(),
            "notes" => Resource::byUser($id, true, Auth::facultyScope()),
            "answers" => (int) Database::value("SELECT COUNT(*) FROM answers WHERE user_id = ? AND is_hidden = 0", [$id]),
            "accepted" => (int) Database::value("SELECT COUNT(*) FROM answers WHERE user_id = ? AND is_accepted = 1 AND is_hidden = 0", [$id]),
            "groups" => (int) Database::value("SELECT COUNT(*) FROM group_members WHERE user_id = ?", [$id]),
        ], "app");
    }

    /** Edit profile and change password live on one settings page. */
    public function edit(): void
    {
        $this->view("profile/edit", [
            "title" => "Settings | Ncircle",
            "pageCss" => "profile",
            "user" => Auth::user(),
        ], "app");
    }

    public function update(): void
    {
        $data = $this->validate([
            "name" => "required|min:3|max:100",
            "academic_year" => "required|in:1,2,3,4",
            "bio" => "max:500",
            "skills" => "max:255",
        ], ["academic_year" => "academic year"]);
        User::updateProfile(Auth::id(), [
            "name" => $data["name"],
            "academic_year" => $data["academic_year"],
            "bio" => $data["bio"] ?? "",
            "skills" => $data["skills"] ?? "",
            "open_to_help" => !empty($data["open_to_help"]),
            "listed_in_directory" => !empty($data["listed_in_directory"]),
        ]);
        Flash::add("success", "Profile updated.");
        Response::redirect("/profile/edit");
    }

    public function updatePhoto(): void
    {
        $old = Auth::user()["avatar"];
        $result = AvatarService::store($_FILES["photo"] ?? null);
        if (isset($result["error"])) {
            $this->failValidation(["photo" => $result["error"]], []);
        }
        User::setAvatar(Auth::id(), $result["stored"]);
        AvatarService::delete($old);
        Flash::add("success", "Photo updated.");
        Response::redirect("/profile/edit");
    }

    public function deletePhoto(): void
    {
        $old = Auth::user()["avatar"];
        User::setAvatar(Auth::id(), null);
        AvatarService::delete($old);
        Flash::add("success", "Photo removed.");
        Response::redirect("/profile/edit");
    }

    public function photo(int $id): void
    {
        $stored = $this->orNotFound(User::find($id))["avatar"];
        if ($stored === null || !is_file(AvatarService::path($stored))) {
            Response::error(404);
        }
        header("Content-Type: " . AvatarService::mime($stored));
        header("Content-Security-Policy: default-src 'none'; sandbox");
        header("Cache-Control: private, max-age=86400");
        header("Content-Length: " . filesize(AvatarService::path($stored)));
        readfile(AvatarService::path($stored));
    }

    public function updatePassword(): void
    {
        $this->validate([
            "current_password" => "required",
            "password" => "required|password",
            "password_confirmation" => "required|same:password",
        ], ["password_confirmation" => "Confirm password"]);
        if (!password_verify(Request::raw("current_password"), Auth::user()["password_hash"])) {
            $this->failValidation(["current_password" => "That is not your current password."]);
        }
        User::setPassword(Auth::id(), Request::raw("password"));
        User::deleteTokens(Auth::id(), "remember");
        Session::regenerate();
        Flash::add("success", "Password changed.");
        Response::redirect("/profile/edit");
    }

    public function space(): void
    {
        $user = Auth::user();
        $this->view("profile/space", [
            "title" => "Personal study space | Ncircle",
            "pageCss" => "profile",
            "user" => $user,
            "goal" => StatsService::goal($user),
            "logs" => StudyLog::recent((int) $user["id"]),
            "bookmarks" => Resource::bookmarked((int) $user["id"], Auth::facultyScope()),
            "uploads" => Resource::byUser((int) $user["id"]),
            "groups" => Group::forUser((int) $user["id"]),
        ], "app");
    }

    public function logStudy(): void
    {
        $data = $this->validate([
            "minutes" => "required|between:1,720",
            "logged_on" => "required|date",
            "note" => "max:160",
        ], ["logged_on" => "Date"]);
        $date = date("Y-m-d", strtotime($data["logged_on"]));
        if ($date > date("Y-m-d")) {
            $this->failValidation(["logged_on" => "The date cannot be in the future."], $data);
        }
        StudyLog::add(Auth::id(), (int) $data["minutes"], $data["note"] ?? "", $date);
        Flash::add("success", "Study time logged.");
        Response::redirect("/space");
    }

    public function updateGoal(): void
    {
        $data = $this->validate(["goal_hours" => "required|between:1,100"], ["goal_hours" => "Weekly goal"]);
        User::setGoal(Auth::id(), (int) $data["goal_hours"] * 60);
        Flash::add("success", "Weekly goal updated.");
        Response::redirect("/space");
    }
}
