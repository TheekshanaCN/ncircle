<?php
/** app/Controllers/Admin/ModerationController.php  -  queue of content students reported */
namespace Admin;

use Auth, Controller, Flash, Report, Response;

class ModerationController extends Controller
{
    public function index(): void
    {
        $this->view("admin/moderation", [
            "title" => "Moderation | Ncircle",
            "pageCss" => "admin",
            "reports" => Report::open(),
        ], "admin");
    }

    public function hide(int $id): void
    {
        $report = $this->orNotFound(Report::find($id));
        Report::hideTarget($report);
        Report::close($id, "resolved", Auth::id());
        Flash::add("success", "Content removed and report closed.");
        Response::redirect("/admin/moderation");
    }

    public function dismiss(int $id): void
    {
        $this->orNotFound(Report::find($id));
        Report::close($id, "dismissed", Auth::id());
        Flash::add("success", "Report dismissed.");
        Response::redirect("/admin/moderation");
    }
}
