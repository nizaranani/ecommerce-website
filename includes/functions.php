<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- CSRF Protection ----
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_input()
{
    echo '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

// ---- Auth helpers ----
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin()
{
    if (!is_admin()) {
        header('Location: ../index.php');
        exit;
    }
}
