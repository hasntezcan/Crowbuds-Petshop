<?php
// Require user login
include_once("../../includes/user_guard.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Checkout - Crawl Buds PetShop";
$active_page = "checkout";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/checkout.css";
include("../../includes/db_connect.php");

// Cart Logic Redundant? No, we need it to display summary and for order insertion
// Re-calculate cart logic
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    if (!empty($ids)) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $total_price = $p['price'] * $qty;
            $subtotal += $total_price;
            
            $p['qty'] = $qty;
            $p['total_price'] = $total_price;
            $cart_items[] = $p;
        }
    }
}

$shipping = 5.00;
if ($subtotal > 50) $shipping = 0;
$total = $subtotal + $shipping;

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'place_order') {
    // 1. Get User ID (or set random guest ID? DB Requires valid User Key linked to `users`)
    // If not logged in, we cannot place order unless we create a guest user or the DB allows null.
    // Based on schema, orders.user_id is NOT NULL. So User MUST be logged in.
    
    if (!isset($_SESSION['user_id'])) {
        // Redirect to Login
        header("Location: ../../pages/user/login.php");
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $payment_method = $_POST['payment']; // Credit Card or Cash

    try {
        $pdo->beginTransaction();

        // Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, payment_method, shipping_full_name, shipping_address, shipping_phone, order_total, order_status) VALUES (?, NOW(), ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $payment_method, $full_name, $address, $phone, $total]);
        $order_id = $pdo->lastInsertId();

        // Insert Order Items
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($cart_items as $item) {
             $stmt_item->execute([$order_id, $item['id'], $item['qty'], $item['price'], $item['total_price']]);
        }

        $pdo->commit();
        
        // Clear Cart
        $_SESSION['cart'] = [];
        
        // Redirect to Success/My Orders
        header("Location: my_orders.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to place order: " . $e->getMessage();
    }
}

include("../../includes/header.php");
?>

<div class="page-container">
    <h1 class="page-title">Checkout</h1>
    
    <?php if (empty($cart_items)): ?>
         <p>Your cart is empty. <a href="home.php">Go Shopping</a></p>
    <?php else: ?>

    <div class="checkout-layout">
        <div class="forms-column">
            <!-- Order Form -->
            <form method="POST" class="checkout-form-wrapper">
                <input type="hidden" name="action" value="place_order">
                
                <section class="form-section">
                    <h2 class="section-title">Shipping Details</h2>
                     <?php if(!isset($_SESSION['user_id'])): ?>
                        <div style="padding: 1rem; background-color: #fef3c7; border-radius: 0.5rem; margin-bottom: 1rem;">
                            You must be <a href="login.php" style="text-decoration: underline;">logged in</a> to place an order.
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-row span-2">
                        <label class="form-label">Full name <input type="text" name="full_name" class="form-input" required></label>
                    </div>
                    <div class="form-row">
                        <label class="form-label">Phone <input type="tel" name="phone" class="form-input" required></label>
                    </div>
                    <div class="form-row span-2">
                        <label class="form-label">Address <input type="text" name="address" class="form-input" required></label>
                    </div>
                </section>

                <section class="form-section payment-section">
                    <h2 class="section-title">Payment Method</h2>
                    <div class="payment-options">
                        <label class="payment-radio sorted"><input type="radio" name="payment" value="Credit Card" checked> <span>Credit/Debit Card</span></label>
                        <label class="payment-radio"><input type="radio" name="payment" value="Cash on Delivery"> <span>Cash on Delivery</span></label>
                    </div>
                </section>

                <div class="form-actions">
                    <a href="cart.php" class="back-link">Back to cart</a>
                     <?php if(isset($_SESSION['user_id'])): ?>
                        <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                    <?php else: ?>
                         <button type="button" class="btn btn-disabled btn-lg" disabled>Login to Order</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Summary -->
        <div class="summary-column">
            <div class="summary-card sticky-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-items">
                    <?php foreach($cart_items as $item): ?>
                    <div class="summary-item">
                        <img src="../../<?php echo htmlspecialchars($item['image_url']); ?>" class="summary-img">
                        <div class="summary-info">
                            <p class="summary-name"><?php echo htmlspecialchars($item['name']); ?></p>
                            <span class="summary-qty">Qty: <?php echo $item['qty']; ?></span>
                        </div>
                        <span class="summary-price">$<?php echo number_format($item['price'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-totals">
                    <div class="total-row"><span>Subtotal</span> <span class="value">$<?php echo number_format($subtotal, 2); ?></span></div>
                    <div class="total-row"><span>Shipping</span> <span class="value">$<?php echo number_format($shipping, 2); ?></span></div>
                    <div class="grand-total"><span>Total</span> <span class="value">$<?php echo number_format($total, 2); ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include("../../includes/footer.php"); ?>