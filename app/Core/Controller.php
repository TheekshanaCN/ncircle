<?php
/**
 * app/Core/Controller.php  -  base class for every controller (Core lead only)
 */
abstract class Controller
{
    /**
     * Render views/<name>.php inside a layout.
     * $data keys become variables in the view, e.g. ['pageCss' => 'landing'].
     */
    protected function view(string $name, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/views/' . $name . '.php';
        $content = ob_get_clean();   // the layout prints $content

        require BASE_PATH . '/views/layouts/' . $layout . '.php';
    }
}
