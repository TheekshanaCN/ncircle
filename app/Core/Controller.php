<?php
/**
 * app/Core/Controller.php  -  base class for every controller
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

    /** Validate POST data. On failure go back to the form with errors and old input. */
    protected function validate(array $rules, array $labels = []): array
    {
        $data = Request::all();
        $errors = Validator::check($data, $rules, $labels);
        if ($errors) {
            $this->failValidation($errors, $data);
        }
        return $data;
    }

    protected function failValidation(array $errors, ?array $data = null): void
    {
        $data = $data ?? Request::all();
        foreach (array_keys($data) as $key) {
            if (stripos((string) $key, 'password') !== false || $key === '_token') {
                unset($data[$key]);
            }
        }
        Session::flash('_errors', $errors);
        Session::flash('_old', $data);
        Response::back();
    }

    /** Load a row or show the 404 page. */
    protected function orNotFound(?array $row): array
    {
        if ($row === null) {
            Response::error(404);
        }
        return $row;
    }

    /** Page number from ?page= */
    protected function page(): int
    {
        return max(1, (int) Request::query('page', 1));
    }
}
