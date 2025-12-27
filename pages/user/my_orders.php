<?php
// Require user login
include_once("../../includes/user_guard.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "My Orders - Crawl Buds PetShop";
$active_page = "my_orders";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/my_orders.css";
include("../../includes/header.php"); // Includes DB connect

$user_id = $_SESSION['user_id'];

// Fetch all orders for this user
// Join with order items for summary? Just fetch orders first.
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY order_date DESC");
$stmt->execute(['uid' => $user_id]);
$orders = $stmt->fetchAll();

// Handle selected order details
$selected_order = null;
$order_items = [];

if (isset($_GET['order_id'])) {
    $oid = intval($_GET['order_id']);
    // Verify ownership
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :oid AND user_id = :uid");
    $stmt->execute(['oid' => $oid, 'uid' => $user_id]);
    $selected_order = $stmt->fetch();

    if ($selected_order) {
        // Fetch items
        $stmt_items = $pdo->prepare("
            SELECT oi.*, p.name, p.image_url 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :oid
        ");
        $stmt_items->execute(['oid' => $oid]);
        $order_items = $stmt_items->fetchAll();
    }
} elseif (!empty($orders)) {
    // Default to first order
    $selected_order = $orders[0];
    $oid = $selected_order['id'];

    $stmt_items = $pdo->prepare("
            SELECT oi.*, p.name, p.image_url 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :oid
        ");
    $stmt_items->execute(['oid' => $oid]);
    $order_items = $stmt_items->fetchAll();
}
?>

<div class="page-container">
    <div class="page-header-group">
        <h1 class="page-title">My Orders</h1>
    </div>

    <?php if (empty($orders)): ?>
        <p style="text-align:center; padding: 4rem;">You have not placed any orders yet.</p>
    <?php else: ?>
        <div class="orders-layout">
            <!-- List Column -->
            <div class="orders-list-column">
                <!-- Search & Filters -->
                <div class="search-filter-box">
                    <div class="search-bar">
                        <span class="material-symbols-outlined search-icon">search</span>
                        <input type="text" placeholder="Search by Order ID">
                    </div>
                </div>

                <div class="filter-tabs">
                    <button class="filter-chip active">All</button>
                    <!-- Add filter logic via GET params if needed -->
                </div>

                <!-- Order List -->
                <div class="order-list">
                    <?php foreach ($orders as $order):
                        $isActive = ($selected_order && $selected_order['id'] == $order['id']) ? 'active' : '';
                        $statusClass = strtolower($order['order_status']);
                        ?>
                        <a href="my_orders.php?order_id=<?php echo $order['id']; ?>" class="order-card <?php echo $isActive; ?>"
                            style="text-decoration:none; color:inherit; display:block;">
                            <div class="order-card-header">
                                <span class="order-id">#<?php echo $order['id']; ?></span>
                                <span
                                    class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                            </div>
                            <div class="order-card-footer">
                                <span class="order-date"><?php echo date("M d, Y", strtotime($order['order_date'])); ?></span>
                                <span class="order-price">$<?php echo number_format($order['order_total'], 2); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Details Column -->
            <?php if ($selected_order): ?>
                <div class="orders-detail-column">
                    <div class="detail-card sticky-detail">
                        <div class="detail-header">
                            <h2 class="detail-title">Order Details</h2>
                            <span class="detail-subtitle">Order #<?php echo $selected_order['id']; ?></span>
                        </div>

                        <div class="info-grid">
                            <div class="info-block">
                                <h3>Shipping Address</h3>
                                <p><?php echo htmlspecialchars($selected_order['shipping_full_name']); ?><br>
                                    <?php echo htmlspecialchars($selected_order['shipping_address']); ?><br>
                                    <?php echo htmlspecialchars($selected_order['shipping_phone']); ?>
                                </p>
                            </div>
                            <div class="info-block">
                                <h3>Payment Method</h3>
                                <p><?php echo htmlspecialchars($selected_order['payment_method']); ?></p>
                            </div>
                        </div>

                        <div class="items-section">
                            <h3>Items Ordered</h3>
                            <div class="items-list">
                                <?php foreach ($order_items as $item):
                                    $img = "../../" . htmlspecialchars($item['image_url']);
                                    ?>
                                    <div class="item-row">
                                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                            class="item-thumb">
                                        <div class="item-details">
                                            <div class="item-top">
                                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                                <span
                                                    class="item-price">$<?php echo number_format($item['unit_price'], 2); ?></span>
                                            </div>
                                            <span class="item-qty">Qty: <?php echo $item['quantity']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="totals-section">
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span>$<?php echo number_format($selected_order['order_total'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include("../../includes/footer.php"); ?>