<?php

require_once __DIR__ . '/functions.php';

function require_login()
{
    if (empty($_SESSION['user_id'])) {
        redirect('/home_services/login.php');
    }
}

function require_role($role)
{
    require_login();

    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        exit("Access denied. You do not have permission to view this page.");
    }
}