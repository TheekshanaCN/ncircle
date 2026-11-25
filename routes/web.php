<?php
/**
 * routes/web.php  -  every URL on the site.
 * Middleware: 'guest' signed-out only, 'auth' signed-in, 'admin' admins. POST routes are always CSRF-checked.
 */

$router->get("/", "HomeController@index");

if (defined("APP_DEBUG") && APP_DEBUG) {
    $router->get("/dev/styleguide", "DevController@styleguide");
}

/* ---------- Live numbers ---------- */
$router->get("/api/stats/landing", "StatsController@landing");
$router->get("/api/stats/dashboard", "StatsController@dashboard", ["auth"]);

/* ---------- Public pages ---------- */
$router->get("/terms", "PageController@terms");
$router->get("/privacy", "PageController@privacy");
$router->get("/integrity", "PageController@integrity");
$router->get("/help", "PageController@help");

/* ---------- Auth ---------- */
$router->get("/login", "AuthController@showLogin", ["guest"]);
$router->post("/login", "AuthController@login", ["guest"]);
$router->get("/register", "AuthController@showRegister", ["guest"]);
$router->post("/register", "AuthController@register", ["guest"]);
$router->post("/logout", "AuthController@logout");
$router->get("/check-inbox", "AuthController@checkInbox", ["guest"]);
$router->post("/verify-email/resend", "AuthController@resend", ["guest"]);
$router->get("/verify-email", "AuthController@verify");
$router->get("/forgot-password", "AuthController@showForgot", ["guest"]);
$router->post("/forgot-password", "AuthController@forgot", ["guest"]);
$router->get("/reset-password", "AuthController@showReset", ["guest"]);
$router->post("/reset-password", "AuthController@reset", ["guest"]);

/* ---------- Student dashboard and search ---------- */
$router->get("/dashboard", "DashboardController@index", ["auth"]);
$router->get("/search", "SearchController@index", ["auth"]);

/* ---------- Notes ---------- */
$router->get("/resources", "ResourceController@index", ["auth"]);
$router->get("/resources/create", "ResourceController@create", ["auth"]);
$router->post("/resources", "ResourceController@store", ["auth"]);
$router->get("/resources/{id}", "ResourceController@show", ["auth"]);
$router->get("/resources/{id}/download", "ResourceController@download", ["auth"]);
$router->get("/resources/{id}/edit", "ResourceController@edit", ["auth"]);
$router->post("/resources/{id}/edit", "ResourceController@update", ["auth"]);
$router->post("/resources/{id}/delete", "ResourceController@destroy", ["auth"]);
$router->post("/resources/{id}/bookmark", "ResourceController@bookmark", ["auth"]);
$router->post("/resources/{id}/comments", "ResourceController@comment", ["auth"]);
$router->post("/api/resources/{id}/rate", "RatingController@rate", ["auth"]);
$router->post("/flag", "FlagController@store", ["auth"]);

/* ---------- Images, files and GIFs on posts ---------- */
$router->get("/attachments/{id}", "AttachmentController@show", ["auth"]);
$router->get("/api/gifs", "AttachmentController@gifs", ["auth"]);

/* ---------- Forum ---------- */
$router->get("/forum", "ForumController@index", ["auth"]);
$router->get("/forum/create", "ForumController@create", ["auth"]);
$router->post("/forum", "ForumController@store", ["auth"]);
$router->get("/forum/{id}", "ForumController@show", ["auth"]);
$router->get("/forum/{id}/edit", "ForumController@edit", ["auth"]);
$router->post("/forum/{id}/edit", "ForumController@update", ["auth"]);
$router->post("/forum/{id}/delete", "ForumController@destroy", ["auth"]);
$router->post("/forum/{id}/answers", "ForumController@answer", ["auth"]);
$router->post("/answers/{id}/accept", "ForumController@accept", ["auth"]);
$router->post("/answers/{id}/vote", "ForumController@vote", ["auth"]);
$router->get("/answers/{id}/edit", "ForumController@editAnswer", ["auth"]);
$router->post("/answers/{id}/edit", "ForumController@updateAnswer", ["auth"]);
$router->post("/answers/{id}/delete", "ForumController@destroyAnswer", ["auth"]);

/* ---------- Study groups ---------- */
$router->get("/groups", "GroupController@index", ["auth"]);
$router->get("/groups/create", "GroupController@create", ["auth"]);
$router->post("/groups", "GroupController@store", ["auth"]);
$router->get("/groups/{id}", "GroupController@show", ["auth"]);
$router->post("/groups/{id}/join", "GroupController@join", ["auth"]);
$router->post("/groups/{id}/leave", "GroupController@leave", ["auth"]);
$router->get("/groups/{id}/edit", "GroupController@edit", ["auth"]);
$router->post("/groups/{id}/edit", "GroupController@update", ["auth"]);
$router->post("/groups/{id}/delete", "GroupController@destroy", ["auth"]);
$router->post("/groups/{id}/posts", "GroupController@post", ["auth"]);
$router->post("/groups/posts/{id}/delete", "GroupController@deletePost", ["auth"]);

/* ---------- Find Members: the one area shared across every faculty ---------- */
$router->get("/members", "MemberController@requests", ["auth"]);
$router->get("/members/directory", "MemberController@directory", ["auth"]);
$router->get("/members/requests/create", "MemberController@create", ["auth"]);
$router->post("/members/requests", "MemberController@store", ["auth"]);
$router->get("/members/requests/{id}", "MemberController@show", ["auth"]);
$router->post("/members/requests/{id}/respond", "MemberController@respond", ["auth"]);
$router->post("/members/requests/{id}/close", "MemberController@close", ["auth"]);
$router->post("/members/responses/{id}/accept", "MemberController@accept", ["auth"]);
$router->post("/members/responses/{id}/decline", "MemberController@decline", ["auth"]);

/* ---------- Meetings ---------- */
$router->get("/sessions", "SessionController@index", ["auth"]);
$router->get("/sessions/create", "SessionController@create", ["auth"]);
$router->post("/sessions", "SessionController@store", ["auth"]);
$router->get("/sessions/{id}/edit", "SessionController@edit", ["auth"]);
$router->post("/sessions/{id}", "SessionController@update", ["auth"]);
$router->post("/sessions/{id}/join", "SessionController@join", ["auth"]);
$router->post("/sessions/{id}/leave", "SessionController@leave", ["auth"]);
$router->post("/sessions/{id}/delete", "SessionController@destroy", ["auth"]);

/* ---------- Profile, study space, settings, notifications ---------- */
$router->get("/space", "ProfileController@space", ["auth"]);
$router->post("/space/log", "ProfileController@logStudy", ["auth"]);
$router->post("/space/goal", "ProfileController@updateGoal", ["auth"]);
$router->get("/profile", "ProfileController@me", ["auth"]);
$router->get("/profile/edit", "ProfileController@edit", ["auth"]);
$router->post("/profile/edit", "ProfileController@update", ["auth"]);
$router->post("/profile/photo", "ProfileController@updatePhoto", ["auth"]);
$router->post("/profile/photo/delete", "ProfileController@deletePhoto", ["auth"]);
$router->get("/profile/{id}/photo", "ProfileController@photo", ["auth"]);
$router->get("/profile/{id}", "ProfileController@show", ["auth"]);
$router->post("/settings/password", "ProfileController@updatePassword", ["auth"]);
$router->get("/notifications", "NotificationController@index", ["auth"]);
$router->post("/notifications/read-all", "NotificationController@readAll", ["auth"]);
$router->get("/notifications/{id}/open", "NotificationController@open", ["auth"]);

/* ---------- Admin ---------- */
$router->get("/admin", 'Admin\DashboardController@index', ["admin"]);
$router->get("/admin/approvals", 'Admin\ResourceApprovalController@index', ["admin"]);
$router->post("/admin/approvals/{id}/approve", 'Admin\ResourceApprovalController@approve', ["admin"]);
$router->post("/admin/approvals/{id}/reject", 'Admin\ResourceApprovalController@reject', ["admin"]);
$router->get("/admin/users", 'Admin\UserController@index', ["admin"]);
$router->post("/admin/users/{id}/status", 'Admin\UserController@status', ["admin"]);
$router->post("/admin/users/{id}/role", 'Admin\UserController@role', ["admin"]);
$router->post("/admin/users/{id}/faculty", 'Admin\UserController@faculty', ["admin"]);
$router->get("/admin/subjects", 'Admin\SubjectController@index', ["admin"]);
$router->post("/admin/subjects", 'Admin\SubjectController@store', ["admin"]);
$router->post("/admin/subjects/{id}", 'Admin\SubjectController@update', ["admin"]);
$router->post("/admin/subjects/{id}/toggle", 'Admin\SubjectController@toggle', ["admin"]);
$router->get("/admin/moderation", 'Admin\ModerationController@index', ["admin"]);
$router->post("/admin/moderation/{id}/hide", 'Admin\ModerationController@hide', ["admin"]);
$router->post("/admin/moderation/{id}/dismiss", 'Admin\ModerationController@dismiss', ["admin"]);
$router->get("/admin/reports", 'Admin\ReportController@index', ["admin"]);
$router->get("/admin/reports/export", 'Admin\ReportController@export', ["admin"]);
