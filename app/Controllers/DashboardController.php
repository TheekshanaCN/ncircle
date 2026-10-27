<?php
/**
 * app/Controllers/DashboardController.php  -  student dashboard (Dashboard owner)
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view(
            "dashboard/index",
            ["title" => "Dashboard | Ncircle"],
            "app",
        );
    }
}
