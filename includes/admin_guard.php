<?php
/**
 * Admin Guard - Protects admin-only pages
 * Include this at the top of all admin pages
 * 
 * Usage: include_once("../../includes/admin_guard.php");
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login
    header("Location: ../user/login.php");
    exit;
}

// Harden session
require_once(__DIR__ . '/security.php');
hardenSession();
?>