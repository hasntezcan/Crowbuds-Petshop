<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Login - Crawl Buds PetShop";
$hide_nav = true;
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/login.css";

include_once("../../includes/db_connect.php");
include_once("../../includes/security.php");

$error = "";
$success = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $user_type = $_POST['user_type'];

        if ($user_type == 'Customer') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
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

    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm = trim($_POST['confirm_password']);

        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
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
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $extra_css; ?>">
</head>

<body class="login-page">
    <div class="login-container">
        <div class="login-sidebar">
            <div class="sidebar-content">
                <a href="home.php" class="logo-link">
                    <img src="<?php echo $assets_path; ?>/images/logo.png" alt="Logo" class="login-logo">
                    <h2>Crawl Buds PetShop</h2>
                </a>
                <p class="sidebar-text">Welcome to our pet paradise! Shop quality products for your furry friends.</p>
                <a href="shop.php" class="btn btn-ghost">← Back to Shop</a>
            </div>
        </div>

        <div class="login-main">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Login Form (Default) -->
            <div class="form-wrapper" id="loginForm">
                <h1>Welcome Back!</h1>
                <p class="form-subtitle">Log in to your account.</p>

                <form method="POST">
                    <input type="hidden" name="action" value="login">

                    <div class="user-type-toggle">
                        <label class="toggle-option">
                            <input type="radio" name="user_type" value="Customer" checked>
                            <span>Customer</span>
                        </label>
                        <label class="toggle-option">
                            <input type="radio" name="user_type" value="Admin">
                            <span>Admin</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-input password-input" id="loginPassword"
                                required>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('loginPassword', this)">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full btn-lg">Log In</button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="#" onclick="showRegister(); return false;"
                            class="link-primary">Create one</a></p>
                </div>
            </div>

            <!-- Register Form (Hidden by default) -->
            <div class="form-wrapper" id="registerForm" style="display:none;">
                <h1>Create Account</h1>
                <p class="form-subtitle">Join us to track orders and save details.</p>

                <form method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-input" placeholder="e.g. Alex Doe" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-input" placeholder="you@example.com" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" class="form-input password-input"
                                    id="registerPassword" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('registerPassword', this)">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirm</label>
                            <div class="password-wrapper">
                                <input type="password" name="confirm_password" class="form-input password-input"
                                    id="confirmPassword" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('confirmPassword', this)">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full btn-lg">Create Account</button>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="#" onclick="showLogin(); return false;"
                            class="link-primary">Log in</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }

        function showLogin() {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        }

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('.material-symbols-outlined');

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>

</html>