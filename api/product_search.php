<?php
/**
 * AJAX API for product search and filtering
 */
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/db_connect.php');

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 999999;
$in_stock_only = isset($_GET['in_stock']) && $_GET['in_stock'] === 'true';
$sort = $_GET['sort'] ?? 'popularity';
$page = (int) ($_GET['page'] ?? 1);
$per_page = 12;
$offset = ($page - 1) * $per_page;

try {
    $where = ["is_active = 1"];
    $params = [];

    if (!empty($search)) {
        $where[] = "(name LIKE :search OR description LIKE :search)";
        $params['search'] = "%$search%";
    }

    if (!empty($category)) {
        $where[] = "category_id = :category";
        $params['category'] = $category;
    }

    $where[] = "price >= :min_price AND price <= :max_price";
    $params['min_price'] = $min_price;
    $params['max_price'] = $max_price;

    if ($in_stock_only) {
        $where[] = "stock_quantity > 0";
    }

    // Determine ORDER BY
    $order_by = "id DESC"; // Default
    switch ($sort) {
        case 'price_low':
            $order_by = "price ASC";
            break;
        case 'price_high':
            $order_by = "price DESC";
            break;
        case 'name':
            $order_by = "name ASC";
            break;
        case 'newest':
            $order_by = "created_at DESC";
            break;
    }

    $where_sql = implode(' AND ', $where);

    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM products WHERE $where_sql";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get products
    $sql = "SELECT * FROM products WHERE $where_sql ORDER BY $order_by LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'products' => $products,
        'total' => $total,
        'page' => $page,
        'total_pages' => ceil($total / $per_page)
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>