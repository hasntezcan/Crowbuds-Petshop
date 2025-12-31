<?php
/**
 * Coupon validation API endpoint
 */
header('Content-Type: application/json');
session_start();

require_once(__DIR__ . '/../includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['valid' => false, 'message' => 'Invalid request method']);
    exit;
}

$coupon_code = trim($_POST['coupon_code'] ?? '');
$order_total = floatval($_POST['order_total'] ?? 0);

if (empty($coupon_code)) {
    echo json_encode(['valid' => false, 'message' => 'Please enter a coupon code']);
    exit;
}

try {
    // Check if coupon exists and is valid
    $stmt = $pdo->prepare("
        SELECT * FROM coupons 
        WHERE code = :code 
        AND is_active = 1 
        AND (start_date IS NULL OR start_date <= NOW())
        AND (end_date IS NULL OR end_date >= NOW())
        AND (max_usage = 0 OR times_used < max_usage)
    ");
    $stmt->execute(['code' => $coupon_code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        echo json_encode(['valid' => false, 'message' => 'Invalid or expired coupon code']);
        exit;
    }

    // Check minimum order amount
    if ($order_total < $coupon['min_order_amount']) {
        echo json_encode([
            'valid' => false,
            'message' => 'Minimum order amount is $' . number_format($coupon['min_order_amount'], 2)
        ]);
        exit;
    }

    // Calculate discount
    $discount = 0;
    if ($coupon['discount_type'] === 'percentage') {
        $discount = ($order_total * $coupon['discount_amount']) / 100;
        if ($coupon['max_discount_amount'] && $discount > $coupon['max_discount_amount']) {
            $discount = $coupon['max_discount_amount'];
        }
    } else {
        $discount = $coupon['discount_amount'];
    }

    // Store in session
    $_SESSION['applied_coupon'] = [
        'code' => $coupon_code,
        'discount' => $discount,
        'coupon_id' => $coupon['id']
    ];

    echo json_encode([
        'valid' => true,
        'discount_amount' => number_format($discount, 2),
        'message' => 'Coupon applied successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Coupon validation error: ' . $e->getMessage());
    echo json_encode(['valid' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('Coupon validation error: ' . $e->getMessage());
    echo json_encode(['valid' => false, 'message' => $e->getMessage()]);
}
?>