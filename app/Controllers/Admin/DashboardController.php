<?php
/** app/Controllers/Admin/DashboardController.php */
namespace Admin;

use Controller, Report, ReportService, Resource;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view("admin/dashboard", [
            "title" => "Admin overview | Ncircle",
            "pageCss" => "admin",
            "overview" => ReportService::overview(),
            "pending" => array_slice(Resource::pending(), 0, 5),
            "reports" => array_slice(Report::open(), 0, 5),
        ], "admin");
    }
}
