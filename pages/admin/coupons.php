<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Manage Coupons";
$active_page = "coupons";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $code = strtoupper(trim($_POST['code']));
        $description = trim($_POST['description']);
        $discount = (float) $_POST['discount_amount'];
        $min_order = (float) $_POST['min_order_amount'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $max_usage = (int) $_POST['max_usage'];
        $admin_id = $_SESSION['admin_id'] ?? 1;

        // Validate dates
        if (strtotime($end_date) < strtotime($start_date)) {
            $error = "End date cannot be before start date!";
        } else {
            try {
                // Use actual database column names
                $stmt = $pdo->prepare("INSERT INTO coupons (code, description, discount_amount, min_order_amount, start_date, end_date, max_usage, created_by_admin_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$code, $description, $discount, $min_order, $start_date, $end_date, $max_usage, $admin_id]);
                $success = "Coupon created successfully!";
            } catch (PDOException $e) {
                $error = "Error creating coupon: " . $e->getMessage();
            }
        }
    } elseif ($action == 'toggle') {
        $id = (int) $_POST['coupon_id'];
        $is_active = (int) $_POST['is_active'];
        $stmt = $pdo->prepare("UPDATE coupons SET is_active = ? WHERE id = ?");
        $stmt->execute([$is_active, $id]);
        $success = "Coupon status updated!";
    } elseif ($action == 'delete') {
        $id = (int) $_POST['coupon_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Coupon deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting coupon: " . $e->getMessage();
        }
    }
}

// Fetch all coupons with correct column names
$stmt = $pdo->query("SELECT c.*, a.full_name as admin_name FROM coupons c LEFT JOIN admins a ON c.created_by_admin_id = a.id ORDER BY c.id DESC");
$coupons = $stmt->fetchAll();
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-page-header">
    <h1>Coupons</h1>
    <button class="btn btn-primary" onclick="showAddCouponModal()">+ Create Coupon</button>
</div>

<div class="section-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th class="hide-mobile">Description</th>
                    <th>Discount</th>
                    <th class="hide-mobile">Min Order</th>
                    <th class="hide-tablet">Valid Period</th>
                    <th class="hide-tablet">Usage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($coupon['code']); ?></strong></td>
                        <td><?php echo htmlspecialchars($coupon['description']); ?></td>
                        <td>$<?php echo number_format($coupon['discount_amount'], 2); ?></td>
                        <td>$<?php echo number_format($coupon['min_order_amount'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($coupon['start_date'])); ?> -
                            <?php echo date('M d, Y', strtotime($coupon['end_date'])); ?>
                        </td>
                        <td><?php echo $coupon['times_used'] ?? 0; ?> /
                            <?php echo ($coupon['max_usage'] ?? 0) == 0 ? '∞' : $coupon['max_usage']; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                <input type="hidden" name="is_active" value="<?php echo $coupon['is_active'] ? 0 : 1; ?>">
                                <button type="submit"
                                    class="badge <?php echo $coupon['is_active'] ? 'badge-green' : 'badge-gray'; ?>"
                                    style="border:none;cursor:pointer;">
                                    <?php echo $coupon['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this coupon?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Coupon Modal -->
<div id="addCouponModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('addCouponModal')">&times;</span>
        <h2>Create New Coupon</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>Coupon Code *</label>
                <input type="text" name="code" class="form-input" placeholder="e.g., SAVE20" required
                    style="text-transform:uppercase;">
            </div>

            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-input" placeholder="e.g., 20% off all products">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Discount Amount ($) *</label>
                    <input type="number" step="0.01" name="discount_amount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Min Order Amount ($) *</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-input" value="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" id="start_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>End Date *</label>
                    <input type="date" name="end_date" id="end_date" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label>Max Usage (0 = unlimited)</label>
                <input type="number" name="max_usage" class="form-input" value="0">
            </div>

            <button type="submit" class="btn btn-primary">Create Coupon</button>
        </form>
    </div>
</div>

<script>
    function showAddCouponModal() {
        document.getElementById('addCouponModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Date validation
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action=""]');
        if (form) {
            form.addEventListener('submit', function (e) {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;

                if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                    e.preventDefault();
                    alert('End date cannot be before start date!');
                    return false;
                }
            });
        }
    });
</script>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-close {
        float: right;
        font-size: 2rem;
        cursor: pointer;
        color: #999;
    }

    .modal-close:hover {
        color: #333;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .admin-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .admin-table {
        width: 100%;
        font-size: 0.875rem;
    }

    .admin-table th {
        white-space: nowrap;
        padding: 0.75rem 0.5rem;
    }

    .admin-table td {
        padding: 0.75rem 0.5rem;
    }

    @media (max-width: 768px) {
        .hide-mobile {
            display: none;
        }
    }

    @media (max-width: 1024px) {
        .hide-tablet {
            display: none;
        }
    }
</style>

<?php include("../../includes/admin_footer.php"); ?>