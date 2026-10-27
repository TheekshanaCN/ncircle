<?php

$router->get("/", "HomeController@index");

if (defined("APP_DEBUG") && APP_DEBUG) {
    $router->get("/dev/styleguide", "DevController@styleguide");
}

/* ---------- Auth  ---------- */
$router->get("/login", "AuthController@showLogin");
$router->get("/register", "AuthController@showRegister");

/* ---------- Student dashboard (Dashboard owner) ---------- */
$router->get("/dashboard", "DashboardController@index");
