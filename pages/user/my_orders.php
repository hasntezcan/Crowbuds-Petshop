<?php
// Require user login
include_once("../../includes/user_guard.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "My Orders - Crawl Buds PetShop";
$active_page = "orders";
$assets_path = "../../assets";
include("../../includes/db_connect.php");

$user_id = $_SESSION['user_id'];
$success = isset($_GET['success']) ? $_GET['success'] : '';

// Fetch user's orders with items
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include("../../includes/header.php");
?>

<?php if ($success): ?>
    <script>Notify.success('Order placed successfully! 🎉');</script>
<?php endif; ?>

<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">My Orders</h1>
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <span class="material-symbols-outlined"
                style="font-size: 4rem; color: var(--color-gray-400);">shopping_bag</span>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders. Start shopping to see your orders here!</p>
            <a href="shop.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order):
                // Get order items
                $stmt_items = $pdo->prepare("
                    SELECT oi.*, p.name, p.image_url
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt_items->execute([$order['id']]);
                $items = $stmt_items->fetchAll();

                // Status badge color
                $status = $order['order_status'];
                $statusClass = 'status-pending';
                if ($status == 'Completed' || $status == 'Delivered') {
                    $statusClass = 'status-completed';
                } elseif ($status == 'Processing' || $status == 'Shipped') {
                    $statusClass = 'status-processing';
                } elseif ($status == 'Cancelled') {
                    $statusClass = 'status-cancelled';
                }
                ?>

                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h3>
                            <span class="order-date">
                                <span class="material-symbols-outlined">calendar_today</span>
                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>

                    <div class="order-items">
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <img src="../../<?php echo htmlspecialchars($item['image_url']); ?>" alt="" class="item-image">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?> ×
                                        $<?php echo number_format($item['unit_price'], 2); ?></p>
                                </div>
                                <div class="item-total">
                                    $<?php echo number_format($item['line_total'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-footer">
                        <div class="shipping-info">
                            <span class="material-symbols-outlined">local_shipping</span>
                            <div>
                                <strong><?php echo htmlspecialchars($order['shipping_full_name']); ?></strong>
                                <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                <p><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                            </div>
                        </div>

                        <div class="payment-info">
                            <span class="material-symbols-outlined">
                                <?php echo $order['payment_method'] == 'Credit Card' ? 'credit_card' : 'payments'; ?>
                            </span>
                            <div>
                                <strong><?php echo $order['payment_method']; ?></strong>
                                <p class="order-total">Total: $<?php echo number_format($order['order_total'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--radius-lg);
    }

    .empty-state h2 {
        margin: 1rem 0 0.5rem;
        font-size: 1.5rem;
    }

    .empty-state p {
        color: var(--color-gray-600);
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--color-gray-200);
        background: var(--color-gray-50);
    }

    .order-info h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .order-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-gray-600);
        font-size: 0.875rem;
    }

    .order-date .material-symbols-outlined {
        font-size: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-processing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .order-items {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--color-gray-50);
        border-radius: var(--radius-sm);
    }

    .item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        background: white;
    }

    .item-info {
        flex: 1;
    }

    .item-info h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .item-info p {
        font-size: 0.875rem;
        color: var(--color-gray-600);
    }

    .item-total {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-primary);
    }

    .order-footer {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        padding: 1.5rem;
        border-top: 1px solid var(--color-gray-200);
        background: var(--color-gray-50);
    }

    .shipping-info,
    .payment-info {
        display: flex;
        gap: 0.75rem;
    }

    .shipping-info .material-symbols-outlined,
    .payment-info .material-symbols-outlined {
        font-size: 1.5rem;
        color: var(--color-primary);
    }

    .shipping-info strong,
    .payment-info strong {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .shipping-info p,
    .payment-info p {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        margin: 0;
    }

    .order-total {
        font-size: 1rem !important;
        font-weight: 700 !important;
        color: var(--color-primary) !important;
        margin-top: 0.5rem !important;
    }

    @media (max-width: 768px) {
        .order-footer {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>

<?php include("../../includes/footer.php"); ?>