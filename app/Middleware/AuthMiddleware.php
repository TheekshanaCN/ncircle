<?php
/** app/Middleware/AuthMiddleware.php  -  signed-in, active users only */
class AuthMiddleware
{
    public function handle(): void
    {
        if (Auth::user() === null) {
            if (Request::wantsJson()) {
                Response::json(['success' => false, 'message' => 'Please sign in first.', 'data' => (object) []], 401);
            }
            Flash::add('info', 'Please sign in to continue.');
            Response::redirect('/login');
        }
    }
}
