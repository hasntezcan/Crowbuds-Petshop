<?php
/**
 * Validate coupon code
 */
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$code = strtoupper(trim($_POST['code'] ?? ''));
$cart_total = (float) ($_POST['cart_total'] ?? 0);

try {
    $stmt = $pdo->prepare("
        SELECT * FROM coupons 
        WHERE code = :code 
        AND is_active = 1 
        AND start_date <= CURDATE() 
        AND end_date >= CURDATE()
        AND (max_usage = 0 OR times_used < max_usage)
    ");
    $stmt->execute(['code' => $code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code']);
        exit;
    }

    if ($cart_total < $coupon['min_order_amount']) {
        echo json_encode([
            'success' => false,
            'message' => 'Minimum order amount $' . number_format($coupon['min_order_amount'], 2) . ' required'
        ]);
        exit;
    }

    $discount = $coupon['discount_amount'];
    $new_total = max(0, $cart_total - $discount);

    echo json_encode([
        'success' => true,
        'message' => 'Coupon applied successfully!',
        'coupon_id' => $coupon['id'],
        'discount' => $discount,
        'new_total' => $new_total
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error validating coupon']);
}
?>