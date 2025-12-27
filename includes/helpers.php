<?php
/**
 * Helper functions for Crawl Buds PetShop
 */

/**
 * Format price with currency symbol
 * @param float $price Price value
 * @param string $currency Currency symbol
 * @return string Formatted price
 */
function formatPrice($price, $currency = '$')
{
    return $currency . number_format($price, 2);
}

/**
 * Format date in readable format
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y')
{
    return date($format, strtotime($date));
}

/**
 * Get stock status badge HTML
 * @param int $quantity Stock quantity
 * @return string HTML for stock badge
 */
function getStockStatus($quantity)
{
    if ($quantity > 10) {
        return '<span class="status-badge status-instock">In Stock</span>';
    } elseif ($quantity > 0) {
        return '<span class="status-badge status-lowstock">Low Stock</span>';
    } else {
        return '<span class="status-badge status-outstock">Out of Stock</span>';
    }
}

/**
 * Resolve image path based on current location
 * @param string $dbPath Path from database (e.g., 'assets/images/product.png')
 * @return string Correct relative path
 */
function resolveImagePath($dbPath)
{
    // If we're in pages/user/ or pages/admin/, prepend ../../
    if (strpos($dbPath, 'assets/') === 0) {
        return '../../' . $dbPath;
    }
    return $dbPath;
}

/**
 * Get cart count for logged-in user
 * @param PDO $pdo Database connection
 * @param int $userId User ID
 * @return int Number of items in cart
 */
function getCartCount($pdo, $userId)
{
    if (!$userId)
        return 0;

    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(ci.quantity), 0) as total
            FROM shopping_carts sc
            JOIN cart_items ci ON sc.id = ci.cart_id
            WHERE sc.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return (int) $result['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Sanitize filename for upload
 * @param string $filename Original filename
 * @return string Safe filename
 */
function sanitizeFilename($filename)
{
    // Remove anything which isn't a word, whitespace, number or any of the following chars: . - _
    $filename = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
    // Remove multiple consecutive dots
    $filename = preg_replace("([\.]{2,})", '', $filename);
    return $filename;
}

/**
 * Generate order reference number
 * @return string Order reference
 */
function generateOrderReference()
{
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>