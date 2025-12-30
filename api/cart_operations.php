<?php
/**
 * AJAX API for cart operations (add, remove, update quantity)
 */
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../includes/db_connect.php');
require_once(__DIR__ . '/../includes/security.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $product_id = (int) $_POST['product_id'];
            $quantity = (int) ($_POST['quantity'] ?? 1);

            // Get or create cart
            $stmt = $pdo->prepare("SELECT id FROM shopping_carts WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $cart = $stmt->fetch();

            if (!$cart) {
                $stmt = $pdo->prepare("INSERT INTO shopping_carts (user_id) VALUES (:user_id)");
                $stmt->execute(['user_id' => $user_id]);
                $cart_id = $pdo->lastInsertId();
            } else {
                $cart_id = $cart['id'];
            }

            // Get product price
            $stmt = $pdo->prepare("SELECT price, stock_quantity FROM products WHERE id = :id AND is_active = 1");
            $stmt->execute(['id' => $product_id]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception('Product not found');
            }

            if ($product['stock_quantity'] < $quantity) {
                throw new Exception('Insufficient stock');
            }

            $item_total = $product['price'] * $quantity;

            // Check if item already in cart
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id");
            $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update quantity - CHECK STOCK FOR NEW TOTAL
                $new_quantity = $existing['quantity'] + $quantity;

                // Validate new total quantity against stock
                if ($product['stock_quantity'] < $new_quantity) {
                    throw new Exception('Insufficient stock. Available: ' . $product['stock_quantity'] . ', In cart: ' . $existing['quantity']);
                }

                $new_total = $product['price'] * $new_quantity;
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity, item_total = :total WHERE id = :id");
                $stmt->execute(['quantity' => $new_quantity, 'total' => $new_total, 'id' => $existing['id']]);
            } else {
                // Insert new item
                $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, item_total) VALUES (:cart_id, :product_id, :quantity, :total)");
                $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $quantity, 'total' => $item_total]);
            }

            // Update cart total
            updateCartTotal($pdo, $cart_id);

            echo json_encode(['success' => true, 'message' => 'Item added to cart']);
            break;

        case 'update':
            $item_id = (int) $_POST['item_id'];
            $quantity = (int) $_POST['quantity'];

            if ($quantity < 1) {
                throw new Exception('Invalid quantity');
            }

            // Get item and product info
            $stmt = $pdo->prepare("
                SELECT ci.cart_id, p.price, p.stock_quantity 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.id = :id
            ");
            $stmt->execute(['id' => $item_id]);
            $item = $stmt->fetch();

            if (!$item) {
                throw new Exception('Item not found');
            }

            if ($item['stock_quantity'] < $quantity) {
                throw new Exception('Insufficient stock');
            }

            $new_total = $item['price'] * $quantity;
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity, item_total = :total WHERE id = :id");
            $stmt->execute(['quantity' => $quantity, 'total' => $new_total, 'id' => $item_id]);

            updateCartTotal($pdo, $item['cart_id']);

            echo json_encode(['success' => true, 'message' => 'Cart updated']);
            break;

        case 'remove':
            $item_id = (int) $_POST['item_id'];

            $stmt = $pdo->prepare("SELECT cart_id FROM cart_items WHERE id = :id");
            $stmt->execute(['id' => $item_id]);
            $item = $stmt->fetch();

            if (!$item) {
                throw new Exception('Item not found');
            }

            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = :id");
            $stmt->execute(['id' => $item_id]);

            updateCartTotal($pdo, $item['cart_id']);

            echo json_encode(['success' => true, 'message' => 'Item removed']);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function updateCartTotal($pdo, $cart_id)
{
    $stmt = $pdo->prepare("SELECT SUM(item_total) as total FROM cart_items WHERE cart_id = :cart_id");
    $stmt->execute(['cart_id' => $cart_id]);
    $result = $stmt->fetch();
    $total = $result['total'] ?? 0;

    $stmt = $pdo->prepare("UPDATE shopping_carts SET cart_total = :total WHERE id = :cart_id");
    $stmt->execute(['total' => $total, 'cart_id' => $cart_id]);
}
?>