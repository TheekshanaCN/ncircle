<?php
/**
 * app/Controllers/AuthController.php  -  sign in, register, email verification, password reset
 */
class AuthController extends Controller
{
    private const RESEND_SECONDS = 60;
    private const MAIL_FAILED = "We could not send the verification email. Please try again in a moment.";

    public function showLogin(): void
    {
        $this->view("auth/login", ["title" => "Sign in | Ncircle"], "auth");
    }

    public function login(): void
    {
        $login = trim((string) Request::input("login"));
        $password = Request::raw("password");

        if ($login === "" || $password === "") {
            $this->failValidation([
                "login" => $login === "" ? "Enter your university email or student ID." : "",
                "password" => $password === "" ? "Enter your password." : "",
            ]);
        }
        if (User::tooManyAttempts($login)) {
            $this->failLogin("Too many attempts. Please wait " . User::ATTEMPT_WINDOW_MINUTES . " minutes and try again.");
        }

        $user = User::findByLogin($login);
        // Unknown accounts still do a hash, so response time does not reveal which emails exist.
        $valid = $user !== null ? password_verify($password, $user["password_hash"]) : password_hash($password, PASSWORD_DEFAULT) === "";
        if (!$valid) {
            User::recordFailedAttempt($login);
            $this->failLogin("Incorrect email or password. Please try again.");
        }
        if ($user["status"] !== "active") {
            $this->failLogin("This account has been blocked. Contact a batch representative.");
        }
        if ($user["email_verified_at"] === null) {
            Session::set("pending_user_id", (int) $user["id"]);
            Flash::add("warning", "Please verify your email before signing in.");
            Response::redirect("/check-inbox");
        }

        User::clearAttempts($login);
        Auth::login($user, Request::input("remember") !== "");
        Response::redirect($user["role"] === "admin" ? "/admin" : "/dashboard");
    }

    private function failLogin(string $message): void
    {
        Session::flash("_old", ["login" => Request::input("login")]);
        Session::flash("login_error", $message);
        Response::redirect("/login");
    }

    public function showRegister(): void
    {
        $this->view("auth/register", ["title" => "Create account | Ncircle"], "auth");
    }

    public function register(): void
    {
        $faculties = implode(",", array_keys(config("app.faculties")));
        $data = $this->validate([
            "name" => "required|min:3|max:100",
            "student_id" => "required|max:20",
            "email" => "required|email|nsbm_email|max:120",
            "faculty" => "required|in:{$faculties}",
            "academic_year" => "required|in:1,2,3,4",
            "password" => "required|password",
            "password_confirmation" => "required|same:password",
            "honor" => "required",
        ], [
            "name" => "Full name",
            "student_id" => "Student ID",
            "email" => "University email",
            "academic_year" => "academic year",
            "password_confirmation" => "Confirm password",
            "honor" => "Accepting the Honor Code",
        ]);
        $data["password"] = Request::raw("password");

        $errors = [];
        if (!preg_match('/^[A-Za-z]?\d{4,10}$/', $data["student_id"])) {
            $errors["student_id"] = "Enter your NSBM student ID, for example 20481.";
        } elseif (User::studentIdExists($data["student_id"])) {
            $errors["student_id"] = "This student ID is already registered.";
        }
        if (User::findByEmail($data["email"]) !== null) {
            $errors["email"] = "This email is already registered. Try signing in.";
        }
        if ($errors) {
            $this->failValidation($errors, $data);
        }

        $userId = User::create($data);
        Session::set("pending_user_id", $userId);
        if (!$this->sendVerification(User::find($userId))) {
            Flash::add("danger", self::MAIL_FAILED);
        }
        Response::redirect("/check-inbox");
    }

    public function logout(): void
    {
        Auth::logout();
        Flash::add("success", "You have signed out.");
        Response::redirect("/login");
    }

    public function checkInbox(): void
    {
        $user = User::find((int) Session::get("pending_user_id", 0));
        if ($user === null) {
            Response::redirect("/login");
        }
        $this->view("auth/check-inbox", [
            "title" => "Check your inbox | Ncircle",
            "email" => $user["email"],
            "sent" => !Session::get("verify_mail_failed", false),
            "wait" => max(0, (int) Session::get("resend_at", 0) - time()),
            "devLink" => APP_DEBUG && MailService::isLogDriver() ? Session::get("dev_verify_link") : null,
        ], "auth");
    }

    public function resend(): void
    {
        $user = User::find((int) Session::get("pending_user_id", 0));
        if ($user === null) {
            Response::redirect("/login");
        }
        if ((int) Session::get("resend_at", 0) > time()) {
            Flash::add("warning", "Please wait a moment before asking for another link.");
        } elseif ($user["email_verified_at"] === null) {
            if ($this->sendVerification($user)) {
                Flash::add("success", "We sent you a new link.");
            } else {
                Flash::add("danger", self::MAIL_FAILED);
            }
        }
        Response::redirect("/check-inbox");
    }

    private function sendVerification(array $user): bool
    {
        $raw = User::createToken((int) $user["id"], "verify", 1440);
        $link = absolute_url("/verify-email?token=" . $raw);
        $sent = MailService::sendAction($user["email"], "Verify your Ncircle account", [
            "name" => $user["name"],
            "title" => "Verify your email",
            "intro" => "Welcome to Ncircle! Confirm your university email to activate your account.",
            "button" => "Verify my email",
            "link" => $link,
            "note" => "This link works for 24 hours.",
        ]);
        Session::set("verify_mail_failed", !$sent);
        Session::set("resend_at", $sent ? time() + self::RESEND_SECONDS : 0);
        Session::set("dev_verify_link", $link);
        return $sent;
    }

    public function verify(): void
    {
        $raw = (string) Request::query("token");
        $userId = $raw !== "" ? User::tokenOwner($raw, "verify") : null;
        if ($userId === null) {
            Flash::add("danger", "That link is invalid or has expired. Sign in to get a new one.");
            Response::redirect("/login");
        }
        User::markVerified($userId);
        User::deleteTokens($userId, "verify");
        Session::forget("pending_user_id");
        Session::forget("dev_verify_link");
        Session::forget("verify_mail_failed");
        Flash::add("success", "Email verified. You can sign in now.");
        Response::redirect("/login");
    }

    public function showForgot(): void
    {
        $this->view("auth/forgot", [
            "title" => "Forgot password | Ncircle",
            "devLink" => APP_DEBUG && MailService::isLogDriver() ? Session::peek("dev_reset_link") : null,
        ], "auth");
    }

    public function forgot(): void
    {
        $data = $this->validate(["email" => "required|email"], ["email" => "University email"]);
        $user = User::findByEmail($data["email"]);
        if ($user !== null && $user["status"] === "active") {
            $raw = User::createToken((int) $user["id"], "reset", 60);
            $link = absolute_url("/reset-password?token=" . $raw);
            MailService::sendAction($user["email"], "Reset your Ncircle password", [
                "name" => $user["name"],
                "title" => "Reset your password",
                "intro" => "Use the link below to choose a new password for your Ncircle account.",
                "button" => "Choose a new password",
                "link" => $link,
                "note" => "This link works for 1 hour. Your password stays the same until you use it.",
            ]);
            Session::flash("dev_reset_link", $link);
        }
        // Same message either way, so this form cannot be used to discover accounts.
        Flash::add("success", "If that email is registered, a reset link is on its way.");
        Response::redirect("/forgot-password");
    }

    public function showReset(): void
    {
        $raw = (string) Request::query("token");
        if ($raw === "" || User::tokenOwner($raw, "reset") === null) {
            Flash::add("danger", "That reset link is invalid or has expired.");
            Response::redirect("/forgot-password");
        }
        $this->view("auth/reset", ["title" => "Choose a new password | Ncircle", "token" => $raw], "auth");
    }

    public function reset(): void
    {
        $raw = (string) Request::input("token");
        $userId = $raw !== "" ? User::tokenOwner($raw, "reset") : null;
        if ($userId === null) {
            Flash::add("danger", "That reset link is invalid or has expired.");
            Response::redirect("/forgot-password");
        }
        $this->validate([
            "password" => "required|password",
            "password_confirmation" => "required|same:password",
        ], ["password_confirmation" => "Confirm password"]);

        User::setPassword($userId, Request::raw("password"));
        User::deleteTokens($userId, "reset");
        User::deleteTokens($userId, "remember");
        Flash::add("success", "Password updated. You can sign in now.");
        Response::redirect("/login");
    }
}
