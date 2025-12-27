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

$user_id = $_SESSION['user_id'];

// Get cart from database
$stmt = $pdo->prepare("SELECT id FROM shopping_carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

$cart_items = [];
$subtotal = 0;

if ($cart) {
    $cart_id = $cart['id'];

    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.description, p.price, p.image_url
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cart_items = $stmt->fetchAll();

    foreach ($cart_items as $item) {
        $subtotal += $item['item_total'];
    }
}

$shipping = ($subtotal > 50 || $subtotal == 0) ? 0 : 5.00;
$total = $subtotal + $shipping;

// Handle Order Submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'place_order') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $payment_method = $_POST['payment_method'];

    // Validate credit card if selected
    if ($payment_method == 'Credit Card') {
        $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
        $card_name = trim($_POST['card_name']);
        $card_expiry = $_POST['card_expiry'];
        $card_cvv = $_POST['card_cvv'];

        // Validation
        if (!preg_match('/^[0-9]{16}$/', $card_number)) {
            $error = "Invalid card number. Must be 16 digits.";
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $card_expiry)) {
            $error = "Invalid expiry date. Format: MM/YY";
        } elseif (!preg_match('/^[0-9]{3,4}$/', $card_cvv)) {
            $error = "Invalid CVV. Must be 3-4 digits.";
        }
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            // Insert Order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, payment_method, shipping_full_name, shipping_address, shipping_phone, order_total, order_status) VALUES (?, NOW(), ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $payment_method, $full_name, $address, $phone, $total]);
            $order_id = $pdo->lastInsertId();

            // Insert Order Items from cart
            $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?)");

            foreach ($cart_items as $item) {
                $stmt_item->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['item_total']]);
            }

            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $stmt->execute([$cart_id]);

            $pdo->commit();

            // Redirect to orders
            header("Location: my_orders.php?success=1");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to place order. Please try again.";
        }
    }
}

include("../../includes/header.php");
?>

<?php if ($error): ?>
    <script>Notify.error('<?php echo addslashes($error); ?>');</script>
<?php endif; ?>

<div class="page-container">
    <h1 class="page-title">Checkout</h1>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 4rem;">
            <h2>Your cart is empty.</h2>
            <a href="shop.php" class="btn btn-primary" style="margin-top: 1rem;">Start Shopping</a>
        </div>
    <?php else: ?>

        <div class="checkout-layout">
            <div class="forms-column">
                <form method="POST" class="checkout-form" id="checkoutForm">
                    <input type="hidden" name="action" value="place_order">

                    <!-- Shipping Details -->
                    <section class="form-section">
                        <h2 class="section-title">Shipping Details</h2>

                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="tel" name="phone" class="form-input" pattern="[0-9]{10,11}" required>
                                <small>10-11 digits</small>
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-input" value="Istanbul" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Address *</label>
                            <textarea name="address" class="form-input" rows="2" required></textarea>
                        </div>
                    </section>

                    <!-- Payment Method -->
                    <section class="form-section">
                        <h2 class="section-title">Payment Methodµ</h2>

                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="Credit Card" checked
                                    onchange="toggleCardForm(true)">
                                <span class="option-content">
                                    <span class="material-symbols-outlined">credit_card</span>
                                    <span>Credit/Debit Card</span>
                                </span>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="Cash on Delivery"
                                    onchange="toggleCardForm(false)">
                                <span class="option-content">
                                    <span class="material-symbols-outlined">payments</span>
                                    <span>Cash on Delivery</span>
                                </span>
                            </label>
                        </div>

                        <!-- Card Details (shown only if Credit Card selected) -->
                        <div id="cardDetails" class="card-details">
                            <div class="form-group">
                                <label>Card Number *</label>
                                <input type="text" name="card_number" class="form-input" id="cardNumber"
                                    placeholder="1234 5678 9012 3456" maxlength="19" pattern="[0-9 ]{16,19}"
                                    oninput="formatCardNumber(this)">
                                <small id="cardError" class="error-text"></small>
                            </div>

                            <div class="form-group">
                                <label>Cardholder Name *</label>
                                <input type="text" name="card_name" class="form-input" placeholder="JOHN DOE"
                                    pattern="[A-Za-z ]+">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Expiry Date *</label>
                                    <input type="text" name="card_expiry" class="form-input" id="cardExpiry"
                                        placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                                        oninput="formatExpiry(this)">
                                </div>
                                <div class="form-group">
                                    <label>CVV *</label>
                                    <input type="text" name="card_cvv" class="form-input" placeholder="123" maxlength="4"
                                        pattern="[0-9]{3,4}">
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" class="btn btn-primary btn-lg">Place Order
                            ($<?php echo number_format($total, 2); ?>)</button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="summary-column">
                <div class="summary-card sticky-summary">
                    <h3 class="summary-title">Order Summary</h3>

                    <div class="summary-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <img src="../../<?php echo htmlspecialchars($item['image_url']); ?>" class="summary-img">
                                <div class="summary-info">
                                    <p class="summary-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <span class="summary-qty">Qty: <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="summary-price">$<?php echo number_format($item['item_total'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-totals">
                        <div class="total-row"><span>Subtotal</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="total-row"><span>Shipping</span>
                            <span>$<?php echo number_format($shipping, 2); ?></span></div>
                        <div class="grand-total"><span>Total</span> <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleCardForm(show) {
        const cardDetails = document.getElementById('cardDetails');
        const inputs = cardDetails.querySelectorAll('input');

        if (show) {
            cardDetails.style.display = 'block';
            inputs.forEach(input => input.required = true);
        } else {
            cardDetails.style.display = 'none';
            inputs.forEach(input => input.required = false);
        }
    }

    function formatCardNumber(input) {
        let value = input.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        input.value = formattedValue;

        // Validate
        const cardError = document.getElementById('cardError');
        if (value.length > 0 && value.length != 16) {
            cardError.textContent = 'Card number must be 16 digits';
        } else {
            cardError.textContent = '';
        }
    }

    function formatExpiry(input) {
        let value = input.value.replace(/\//g, '');
        if (value.length >= 2) {
            input.value = value.substring(0, 2) + '/' + value.substring(2, 4);
        } else {
            input.value = value;
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        toggleCardForm(true); // Default to card
    });
</script>

<?php include("../../includes/footer.php"); ?>