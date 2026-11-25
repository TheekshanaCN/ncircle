<?php
/** app/Middleware/GuestMiddleware.php  -  sign-in and register pages for signed-out users only */
class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::user() !== null) {
            Response::redirect(Auth::isAdmin() ? '/admin' : '/dashboard');
        }
    }
}
