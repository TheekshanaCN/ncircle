<?php
/** app/Controllers/DashboardController.php  -  student dashboard */
class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $scope = Auth::facultyScope();
        $this->view("dashboard/index", [
            "title" => "Dashboard | Ncircle",
            "pageCss" => "dashboard",
            "user" => $user,
            "live" => StatsService::dashboardLive($user, $scope),
            "mastery" => StatsService::subjectMastery(4, $scope),
            "topNotes" => Resource::topRated(5, $scope),
            "threads" => Question::recent(3, $scope),
            "sessions" => StudySession::upcoming((int) $user["id"], 3, $scope),
        ], "app");
    }
}
