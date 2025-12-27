<?php
// Require user login
include_once("../../includes/user_guard.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Shopping Cart - Crawl Buds PetShop";
$active_page = "cart";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/cart.css";
// DB connection if needed for product details
include("../../includes/db_connect.php");

// --- Cart Logic ---

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($product_id > 0 && $quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
    // Redirect to self to prevent resubmission
    header("Location: cart.php");
    exit;
}

// Handle Remove
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $remove_id = intval($_GET['id']);
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

// Handle Clear
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Fetch Cart Items Details
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    // Get product IDs
    $ids = array_keys($_SESSION['cart']);
    // Create placeholder string (?,?,?)
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

$shipping = ($subtotal > 50 || $subtotal == 0) ? 0 : 5.00;
$total = $subtotal + $shipping;

include("../../includes/header.php");
?>

<div class="page-container">
    <div class="cart-header">
        <nav class="breadcrumb">
            <a href="cart.php" class="current">Cart</a>
            <span class="separator">/</span>
            <span class="disabled">Checkout</span>
            <span class="separator">/</span>
            <span class="disabled">Order Complete</span>
        </nav>

        <div class="page-heading">
            <h1 class="page-title">My Cart (<?php echo count($cart_items); ?> Items)</h1>
            <a href="home.php" class="btn btn-secondary btn-sm">Continue Shopping</a>
        </div>
    </div>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 4rem;">
            <h2>Your cart is empty.</h2>
            <a href="home.php" class="btn btn-primary" style="margin-top: 1rem;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <!-- Cart Items List -->
            <div class="cart-items-column">
                <div class="cart-card">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="col-product">Product</th>
                                <th class="col-price">Price</th>
                                <th class="col-qty">Quantity</th>
                                <th class="col-total">Subtotal</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item):
                                $img = "../../" . htmlspecialchars($item['image_url']);
                                ?>
                                <tr>
                                    <td class="col-product">
                                        <div class="item-details">
                                            <div class="item-image" style="background-image: url('<?php echo $img; ?>');"></div>
                                            <div class="item-info">
                                                <h4 class="item-name"><a
                                                        href="product_detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                                                </h4>
                                                <span class="item-sub">Category ID: <?php echo $item['category_id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-price">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="col-qty">
                                        <span style="font-weight: 600;"><?php echo $item['qty']; ?></span>
                                    </td>
                                    <td class="col-total">$<?php echo number_format($item['total_price'], 2); ?></td>
                                    <td class="col-action">
                                        <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="delete-btn">
                                            <span class="material-symbols-outlined">delete</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-actions">
                    <a href="cart.php?action=clear" class="clear-cart-btn" onclick="return confirm('Are you sure?');">Clear
                        Cart</a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary-column">
                <div class="summary-card sticky-summary">
                    <h2 class="summary-title">Order Summary</h2>

                    <div class="summary-rows">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span class="amount">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Estimated Shipping</span>
                            <span class="amount">$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="amount">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary btn-full btn-lg">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include("../../includes/footer.php"); ?>