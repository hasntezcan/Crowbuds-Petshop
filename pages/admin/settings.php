<?php
/**
 * Admin Settings Page - Password Change & Profile
 */
include_once("../../includes/admin_guard.php");

$page_title = "Settings";
$active_page = "settings";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");
include_once("../../includes/security.php");

$success = '';
$error = '';
$admin_id = $_SESSION['admin_id'];

// Fetch current admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'update_profile') {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);

        $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $admin_id])) {
            $_SESSION['admin_name'] = $full_name;
            $success = "Profile updated successfully!";
            // Refresh admin data
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch();
        } else {
            $error = "Failed to update profile.";
        }
    }

    if ($action == 'change_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (!verifyPassword($current_pass, $admin['password'])) {
            $error = "Current password is incorrect.";
        } elseif (strlen($new_pass) < 6) {
            $error = "New password must be at least 6 characters.";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match.";
        } else {
            $hashed = hashPassword($new_pass);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed, $admin_id])) {
                $success = "Password changed successfully!";
            } else {
                $error = "Failed to change password.";
            }
        }
    }
}
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="settings-container">
    <h1>Account Settings</h1>

    <!-- Profile Update -->
    <div class="section-card">
        <h2>Profile Information</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-input"
                    value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input"
                    value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <!-- Password Change -->
    <div class="section-card">
        <h2>Change Password</h2>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">

            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-input" minlength="6" required>
                <small>Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<style>
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .settings-container h1 {
        margin-bottom: 2rem;
    }

    .section-card {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-card h2 {
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-group small {
        display: block;
        margin-top: 0.25rem;
        color: #666;
        font-size: 0.875rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
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
</style>

<?php include("../../includes/admin_footer.php"); ?>