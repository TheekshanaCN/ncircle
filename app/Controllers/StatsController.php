<?php
/** app/Controllers/StatsController.php  -  JSON for the live numbers. See SC.live in app.js. */
class StatsController extends Controller
{
    /** Public: totals only, with no names or personal details. */
    public function landing(): void
    {
        Response::json(["success" => true, "message" => "", "data" => StatsService::landingCached()]);
    }

    public function dashboard(): void
    {
        Response::json(["success" => true, "message" => "", "data" => StatsService::dashboardLive(Auth::user(), Auth::facultyScope(), (string) Request::query("range", "week"))]);
    }
}
