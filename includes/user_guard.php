<?php
/**
 * User Guard - Protects user-only pages
 * Include this at the top of pages that require customer login
 * 
 * Usage: include_once("../../includes/user_guard.php");
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    // Redirect to login
    header("Location: login.php?redirect=" . urlencode($_SERVER['PHP_SELF']));
    exit;
}

// Harden session
require_once(__DIR__ . '/security.php');
hardenSession();
?>