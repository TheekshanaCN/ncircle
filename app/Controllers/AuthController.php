<?php
/**
 * app/Controllers/AuthController.php  -  login / register / logout (Auth owner)
 * Only the two GET pages exist for now. POST handlers come later.
 */
class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view("auth/login", ["title" => "Sign in | Ncircle"]);
    }

    public function showRegister(): void
    {
        $this->view("auth/register", ["title" => "Create account | Ncircle"]);
    }
}
