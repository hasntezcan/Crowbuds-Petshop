<?php
/**
 * Get current cart item count for logged-in user
 */
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../includes/db_connect.php');
require_once(__DIR__ . '/../includes/helpers.php');

$user_id = $_SESSION['user_id'] ?? 0;
$count = getCartCount($pdo, $user_id);

echo json_encode(['success' => true, 'count' => $count]);
?>