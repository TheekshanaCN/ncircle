<?php
/** app/Controllers/Admin/SubjectController.php  -  manage NSBM modules */
namespace Admin;

use Controller, Flash, Response, Subject;

class SubjectController extends Controller
{
    public function index(): void
    {
        $this->view("admin/subjects", [
            "title" => "Subjects | Ncircle",
            "pageCss" => "admin",
            "subjects" => Subject::withCounts(),
        ], "admin");
    }

    private function validated(int $exceptId = 0): array
    {
        $faculties = implode(",", array_keys(config("app.faculties")));
        $data = $this->validate([
            "code" => "required|min:3|max:12",
            "name" => "required|min:3|max:100",
            "faculty" => "required|in:{$faculties}",
            "year" => "required|in:1,2,3,4",
        ]);
        if (!preg_match('/^[A-Za-z]{2,5}\d{2,5}$/', $data["code"])) {
            $this->failValidation(["code" => "Use a module code like CS204."], $data);
        }
        if (Subject::codeExists($data["code"], $exceptId)) {
            $this->failValidation(["code" => "That module code already exists."], $data);
        }
        return $data;
    }

    public function store(): void
    {
        Subject::create($this->validated());
        Flash::add("success", "Subject added.");
        Response::redirect("/admin/subjects");
    }

    public function update(int $id): void
    {
        $this->orNotFound(Subject::find($id));
        Subject::update($id, $this->validated($id));
        Flash::add("success", "Subject updated.");
        Response::redirect("/admin/subjects");
    }

    /** Subjects are archived rather than deleted, because notes point at them. */
    public function toggle(int $id): void
    {
        $this->orNotFound(Subject::find($id));
        Subject::toggle($id);
        Flash::add("success", "Subject updated.");
        Response::redirect("/admin/subjects");
    }
}
