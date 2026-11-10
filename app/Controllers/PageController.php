<?php
/** app/Controllers/PageController.php  -  static information pages */
class PageController extends Controller
{
    public function terms(): void
    {
        $this->view("pages/terms", ["title" => "Terms of Service | Ncircle"], Auth::check() ? "app" : "main");
    }

    public function privacy(): void
    {
        $this->view("pages/privacy", ["title" => "Privacy Policy | Ncircle"], Auth::check() ? "app" : "main");
    }

    public function integrity(): void
    {
        $this->view("pages/integrity", ["title" => "Academic Integrity | Ncircle"], Auth::check() ? "app" : "main");
    }

    public function help(): void
    {
        $this->view("pages/help", ["title" => "Help Center | Ncircle"], Auth::check() ? "app" : "main");
    }
}
