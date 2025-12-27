<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Login - Crawl Buds PetShop";
$hide_nav = true;
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/login.css";

// Include DB and security
include_once("../../includes/db_connect.php");
include_once("../../includes/security.php");

$error = "";
$success = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // LOGIN Logic
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $user_type = $_POST['user_type']; // Customer or Admin

        if ($user_type == 'Customer') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            // Use password hashing verification
            if ($user && verifyPassword($password, $user['password'])) {
                // Harden session security
                hardenSession();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'customer';
                header("Location: home.php");
                exit;
            } else {
                $error = "Invalid email or password for Customer.";
            }
        } elseif ($user_type == 'Admin') {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            // Use password hashing verification
            if ($admin && verifyPassword($password, $admin['password'])) {
                hardenSession();

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['role'] = 'admin';
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password for Admin.";
            }
        }
    }

    // REGISTER Logic
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm = trim($_POST['confirm_password']);

        // Validate password length
        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // Check existing
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                // Hash password before storing
                $hashed_password = hashPassword($password);

                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:name, :email, :pass)");
                if ($stmt->execute(['name' => $fullname, 'email' => $email, 'pass' => $hashed_password])) {
                    $success = "Account created! Please log in.";
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../assets/css/pages/login.css" />
</head>

<body>
    <div class="login-layout">
        <header class="login-header">
            <div class="brand-section">
                <div class="brand-logo">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M44 4H30.6666V17.3334H17.3334V30.6666H4V44H44V4Z" fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="brand-title">Crawl Buds PetShop</h2>
            </div>
            <a href="home.php" class="back-link">← Back to shop</a>
        </header>

        <main class="login-content">
            <div class="auth-container">
                <!-- Login Panel -->
                <div class="auth-panel login-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Welcome Back!</h2>
                        <p class="panel-subtitle">Log in to your account.</p>
                        <?php if ($error): ?>
                            <p style="color: var(--color-danger); font-size: 0.875rem; margin-top: 0.5rem;">
                                <?php echo $error; ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <p style="color: var(--color-success); font-size: 0.875rem; margin-top: 0.5rem;">
                                <?php echo $success; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <form class="auth-form" method="POST">
                        <input type="hidden" name="action" value="login">

                        <div class="user-type-switch">
                            <label class="switch-option active">
                                <span>Customer</span>
                                <input type="radio" name="user_type" value="Customer" checked>
                            </label>
                            <label class="switch-option">
                                <span>Admin</span>
                                <input type="radio" name="user_type" value="Admin">
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter your email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="password-input">
                                <input type="password" name="password" placeholder="Enter your password"
                                    class="form-input" required>
                                <button type="button" class="visibility-btn">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox"> Remember me
                            </label>
                            <a href="#" class="forgot-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Log In</button>
                    </form>
                </div>

                <!-- Register Panel -->
                <div class="auth-panel register-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Create Account</h2>
                        <p class="panel-subtitle">Join us to track orders and save details.</p>
                    </div>

                    <form class="auth-form register-form" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="form-row">
                            <div class="form-group span-2">
                                <label>Full Name</label>
                                <input type="text" name="fullname" placeholder="e.g. Alex Doe" class="form-input"
                                    required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group span-2">
                                <label>Email</label>
                                <input type="email" name="email" placeholder="you@example.com" class="form-input"
                                    required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="••••••••" class="form-input"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Confirm</label>
                                <input type="password" name="confirm_password" placeholder="••••••••" class="form-input"
                                    required>
                            </div>
                        </div>

                        <div class="register-footer">
                            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
            <p class="security-note">We protect your data. Your connection is secure.</p>
        </main>
    </div>
</body>

</html>