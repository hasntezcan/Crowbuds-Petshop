<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Customers";
$active_page = "customers"; // Matches link in admin_header logic to be added
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="page-actions-header">
    <h1 class="page-title">Customers</h1>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?php echo $user['id']; ?></td>
                        <td class="cell-primary"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td><?php echo date("M d, Y", strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../../includes/admin_footer.php"); ?>