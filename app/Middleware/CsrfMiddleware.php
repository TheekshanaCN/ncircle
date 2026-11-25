<?php
/** app/Middleware/CsrfMiddleware.php  -  runs on every POST (see Router) */
class CsrfMiddleware
{
    public function handle(): void
    {
        if (Request::postTooLarge()) {
            Flash::add('danger', 'That upload is too large for the server.');
            Response::back();
        }
        $token = Request::input('_token', $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!Csrf::valid(is_string($token) ? $token : '')) {
            if (Request::wantsJson()) {
                Response::error(419);
            }
            Flash::add('danger', 'Your session expired. Please try again.');
            Response::back();
        }
    }
}
