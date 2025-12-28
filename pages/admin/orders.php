<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Orders";
$active_page = "orders";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

// Handle Status Update (Simple implementation via GET for demo, ideally POST)
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $oid = intval($_GET['id']);
    $status = $_GET['status']; // validated against enum in real app
    $stmt = $pdo->prepare("UPDATE orders SET order_status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $oid]);
    header("Location: orders.php");
    exit;
}

// Fetch All Orders
$query = "
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC
";
$orders = $pdo->query($query)->fetchAll();
?>

<div class="page-actions-header">
    <h1 class="page-title">Orders</h1>
    <!-- Validation/Export buttons hidden for MVP -->
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-tabs"
        style="border-bottom: 1px solid var(--color-border-light); padding-bottom: 1rem; margin-bottom: 1rem;">
        <a href="orders.php" class="filter-chip active">All</a>
        <a href="#" class="filter-chip">Pending</a>
        <a href="#" class="filter-chip">Shipped</a>
        <a href="#" class="filter-chip">Delivered</a>
    </div>
</div>

<!-- Orders Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="admin-table orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order):
                    $statusClass = 'badge-gray';
                    if ($order['order_status'] == 'confirmed')
                        $statusClass = 'badge-blue';
                    if ($order['order_status'] == 'shipped')
                        $statusClass = 'badge-blue';
                    if ($order['order_status'] == 'delivered')
                        $statusClass = 'badge-green';
                    if ($order['order_status'] == 'pending')
                        $statusClass = 'badge-yellow';
                    if ($order['order_status'] == 'cancelled')
                        $statusClass = 'badge-red';
                    ?>
                    <tr>
                        <td class="cell-primary">#<?php echo $order['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['username']); ?>
                            <br><span
                                style="font-size:0.8em; color:#666;"><?php echo htmlspecialchars($order['email']); ?></span>
                        </td>
                        <td><?php echo date("M d, Y", strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['order_total'], 2); ?></td>
                        <td><span
                                class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                        </td>
                        <td class="text-right">
                            <div class="dropdown" style="display:inline-block; position:relative;">
                                <button class="btn btn-sm btn-outline">Update Status</button>
                                <div class="dropdown-content"
                                    style="display:none; position:absolute; right:0; background:#fff; border:1px solid #ddd; z-index:1000; box-shadow:0 2px 5px rgba(0,0,0,0.1); min-width:150px; border-radius:0.5rem;">
                                    <a href="orders.php?action=update_status&id=<?php echo $order['id']; ?>&status=shipped"
                                        style="display:block; padding:0.75rem 1rem; text-decoration:none; color:#333; white-space:nowrap;">Mark
                                        Shipped</a>
                                    <a href="orders.php?action=update_status&id=<?php echo $order['id']; ?>&status=delivered"
                                        style="display:block; padding:0.75rem 1rem; text-decoration:none; color:#333; white-space:nowrap;">Mark
                                        Delivered</a>
                                    <a href="orders.php?action=update_status&id=<?php echo $order['id']; ?>&status=cancelled"
                                        style="display:block; padding:0.75rem 1rem; text-decoration:none; color:red; white-space:nowrap;">Cancel</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-footer">
        <span class="pagination-info">Showing <strong><?php echo count($orders); ?></strong> results</span>
    </div>
</div>

<script>
    // Simple script to toggle dropdowns (or just use hover in CSS if preferred, but click is better)
    document.querySelectorAll('.dropdown button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const content = btn.nextElementSibling;
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
    });
</script>

<?php include("../../includes/admin_footer.php"); ?>