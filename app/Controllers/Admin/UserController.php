<?php
/** app/Controllers/Admin/UserController.php  -  block, unblock, promote */
namespace Admin;

use Auth, Controller, Flash, Request, Response, User;

class UserController extends Controller
{
    public function index(): void
    {
        $q = mb_substr((string) Request::query("q"), 0, 100);
        $this->view("admin/users", [
            "title" => "Users | Ncircle",
            "pageCss" => "admin",
            "q" => $q,
            "result" => User::search($q, $this->page()),
            "page" => $this->page(),
        ], "admin");
    }

    public function status(int $id): void
    {
        $user = $this->orNotFound(User::find($id));
        if ($id === Auth::id()) {
            Flash::add("danger", "You cannot block your own account.");
            Response::back("/admin/users");
        }
        $status = $user["status"] === "active" ? "blocked" : "active";
        User::setStatus($id, $status);
        Flash::add("success", $user["name"] . ($status === "blocked" ? " is blocked." : " is unblocked."));
        Response::back("/admin/users");
    }

    /** Only admins can move a student to another faculty. */
    public function faculty(int $id): void
    {
        $user = $this->orNotFound(User::find($id));
        $faculty = (string) Request::input("faculty");
        if (!isset(config("app.faculties")[$faculty])) {
            Flash::add("danger", "Choose a valid faculty.");
            Response::back("/admin/users");
        }
        User::setFaculty($id, $faculty);
        Flash::add("success", $user["name"] . " moved to " . config("app.faculties")[$faculty] . ".");
        Response::back("/admin/users");
    }

    public function role(int $id): void
    {
        $user = $this->orNotFound(User::find($id));
        if ($id === Auth::id()) {
            Flash::add("danger", "You cannot change your own role.");
            Response::back("/admin/users");
        }
        $role = $user["role"] === "admin" ? "student" : "admin";
        User::setRole($id, $role);
        Flash::add("success", $user["name"] . " is now " . ($role === "admin" ? "an admin." : "a student."));
        Response::back("/admin/users");
    }
}
