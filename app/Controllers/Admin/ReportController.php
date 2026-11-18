<?php
/** app/Controllers/Admin/ReportController.php  -  usage reports and CSV export */
namespace Admin;

use Controller, ReportService;

class ReportController extends Controller
{
    public function index(): void
    {
        $this->view("admin/reports", [
            "title" => "Reports | Ncircle",
            "pageCss" => "admin",
            "overview" => ReportService::overview(),
            "topNotes" => ReportService::topNotes(),
            "contributors" => ReportService::topContributors(),
            "subjects" => ReportService::bySubject(),
        ], "admin");
    }

    public function export(): void
    {
        ReportService::exportSubjectsCsv();
    }
}
