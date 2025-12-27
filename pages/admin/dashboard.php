<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Admin Dashboard";
$active_page = "dashboard";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

// Fetch Real Stats
// 1. Today's Orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()");
$today_orders = $stmt->fetchColumn();

// 2. Pending Orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'");
$pending_orders = $stmt->fetchColumn();

// 3. Revenue This Month
$stmt = $pdo->query("SELECT SUM(order_total) FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE()) AND order_status != 'cancelled'");
$monthly_revenue = $stmt->fetchColumn() ?: 0;

// 4. Low Stock Products
$stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 5");
$low_stock = $stmt->fetchColumn();

// Fetch Recent Orders (Limit 5)
$stmt = $pdo->query("
    SELECT o.*, u.username as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();
?>

<!-- Dashboard Stats Grid -->
<div class="dashboard-stats-grid">
    <div class="stat-card">
        <p class="stat-label">Today's Orders</p>
        <p class="stat-value"><?php echo $today_orders; ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Pending Orders</p>
        <p class="stat-value"><?php echo $pending_orders; ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Revenue This Month</p>
        <p class="stat-value">$<?php echo number_format($monthly_revenue, 2); ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Low-Stock Products</p>
        <p class="stat-value"><?php echo $low_stock; ?></p>
    </div>
</div>

<div class="dashboard-content-grid">
    <!-- Chart Section (Static for Demo, but could be dynamic) -->
    <div class="chart-section">
        <div class="section-card chart-card">
            <h3 class="card-title">Last 7 Days (Demo)</h3>
            <div class="chart-container"
                style="height: 15rem; display:flex; align-items:center; justify-content:center; background:#f9fafb;">
                <p style="color:#6b7280;">Chart visualization requires JS library (e.g. Chart.js)</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="recent-orders-section">
        <div class="section-card">
            <h3 class="card-head">Recent Orders</h3>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th><span class="sr-only">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order):
                            $statusMap = [
                                'pending' => 'badge-yellow',
                                'confirmed' => 'badge-blue',
                                'shipped' => 'badge-blue',
                                'delivered' => 'badge-green',
                                'cancelled' => 'badge-red'
                            ];
                            $badgeClass = $statusMap[$order['order_status']] ?? 'badge-gray';
                            ?>
                            <tr>
                                <td class="cell-primary">#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo date("M d, Y", strtotime($order['order_date'])); ?></td>
                                <td>$<?php echo number_format($order['order_total'], 2); ?></td>
                                <td><span
                                        class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                                </td>
                                <td class="text-right"><a href="orders.php" class="action-link">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/admin_footer.php"); ?>