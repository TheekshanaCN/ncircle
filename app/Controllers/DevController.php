<?php
/**
 * app/Controllers/DevController.php  -  developer-only pages (style guide)
 * Routes are only registered when APP_DEBUG is true (see routes/web.php).
 */
class DevController extends Controller
{
    public function styleguide(): void
    {
        $this->view('dev/styleguide', ['title' => 'Style guide | Ncircle']);
    }
}
