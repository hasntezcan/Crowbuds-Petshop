<?php
$page_title = "Account Settings - Crawl Buds PetShop";
$active_page = "settings";
$assets_path = "../../assets";
include("../../includes/user_guard.php");
include("../../includes/header.php");
include("../../includes/security.php");

$success = '';
$error = '';

// Get current user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile
        $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $_SESSION['user_id']])) {
            $success = 'Profile updated successfully!';
            $_SESSION['user_name'] = $full_name;
        } else {
            $error = 'Failed to update profile';
        }
    }

    if (isset($_POST['change_password'])) {
        // Change password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!verifyPassword($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $hashed = hashPassword($new_password);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed, $_SESSION['user_id']])) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password';
            }
        }
    }

    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>

<div class="page-container">
    <div class="page-header">
        <h1>Account Settings</h1>
        <p>Manage your profile and security settings</p>
    </div>

    <div class="settings-content">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="settings-card">
            <h2>Profile Information</h2>
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-input"
                        value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input"
                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <!-- Password Section -->
        <div class="settings-card">
            <h2>Change Password</h2>
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" minlength="6"
                        required>
                    <small>Minimum 6 characters</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>

<style>
    .page-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        text-align: center;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-gray-900);
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: var(--color-gray-600);
    }

    .settings-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .settings-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .settings-card h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--color-gray-900);
    }

    .settings-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--color-gray-700);
    }

    .form-group small {
        font-size: 0.875rem;
        color: var(--color-gray-500);
    }

    .alert {
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>

<?php include("../../includes/footer.php"); ?>