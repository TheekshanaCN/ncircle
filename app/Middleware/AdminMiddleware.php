<?php
/** app/Middleware/AdminMiddleware.php  -  admins only */
class AdminMiddleware
{
    public function handle(): void
    {
        (new AuthMiddleware())->handle();
        if (!Auth::isAdmin()) {
            Response::error(403);
        }
    }
}
