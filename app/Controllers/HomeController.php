<?php
/**
 * app/Controllers/HomeController.php  -  public landing page
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'title'   => 'Ncircle | NSBM StudyCircle',
            'pageCss' => 'landing',   // loads assets/css/pages/landing.css
            'live'    => StatsService::landingCached(),
        ]);
    }
}
