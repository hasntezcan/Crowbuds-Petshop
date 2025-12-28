<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = isset($page_title) ? $page_title : "Crawl Buds PetShop";

// Determine relative paths
if (!isset($assets_path)) {
    // If included from root (e.g. index.php), use ./assets
// If included from pages/user/, use ../../assets
    $assets_path = file_exists("assets/css/style.css") ? "assets" : "../../assets";
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Include DB connection if not already included
$db_path = file_exists("includes/db_connect.php") ? "includes/db_connect.php" : "../../includes/db_connect.php";
if (file_exists($db_path)) {
    include_once($db_path);
}

// Include helpers for cart count
$helpers_path = file_exists("includes/helpers.php") ? "includes/helpers.php" : "../../includes/helpers.php";
if (file_exists($helpers_path)) {
    include_once($helpers_path);
}

// Get cart count
$cart_count = 0;
if ($is_logged_in && isset($pdo) && function_exists('getCartCount')) {
    $cart_count = getCartCount($pdo, $_SESSION['user_id']);
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
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/style.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/layout.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/notifications.css" />
    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?php echo $extra_css; ?>" />
    <?php endif; ?>

    <!-- Scripts -->
    <script src="<?php echo $assets_path; ?>/js/notifications.js" defer></script>
</head>


<body>
    <div class="app-container">
        <!-- TopNavBar -->
        <header class="main-header">
            <div class="header-container">
                <div class="brand-section">
                    <a href="home.php" class="brand-logo">
                        <img src="<?php echo $assets_path; ?>/images/logo.png" alt="Crawl Buds Logo"
                            style="width:50px;height:50px;object-fit:contain;">
                    </a>
                    <h2 class="brand-title">Crawl Buds PetShop</h2>
                </div>

                <nav class="main-nav">
                    <a href="home.php" class="nav-link <?php echo ($active_page == 'home') ? 'active' : ''; ?>">Home</a>
                    <a href="shop.php" class="nav-link <?php echo ($active_page == 'shop') ? 'active' : ''; ?>">Shop</a>
                    <a href="contact.php"
                        class="nav-link <?php echo ($active_page == 'contact') ? 'active' : ''; ?>">Contact Us</a>
                    <a href="about.php" class="nav-link <?php echo ($active_page == 'about') ? 'active' : ''; ?>">About
                        Us</a>
                </nav>

                <div class="header-actions">
                    <?php if ($is_logged_in): ?>
                        <div class="profile-dropdown">
                            <button class="icon-btn profile-trigger" aria-label="Profile">
                                <span class="material-symbols-outlined">person</span>
                            </button>
                            <div class="dropdown-menu">
                                <span class="dropdown-user">Hello, <?php echo htmlspecialchars($user_name); ?>!</span>
                                <a href="<?php echo file_exists('pages/user/my_orders.php') ? 'pages/user/my_orders.php' : 'my_orders.php'; ?>"
                                    class="dropdown-item">
                                    <span class="material-symbols-outlined">receipt_long</span>
                                    My Orders
                                </a>
                                <a href="<?php echo file_exists('pages/user/settings.php') ? 'pages/user/settings.php' : 'settings.php'; ?>"
                                    class="dropdown-item">
                                    <span class="material-symbols-outlined">settings</span>
                                    Settings
                                </a>
                                <a href="<?php echo file_exists('pages/user/logout.php') ? 'pages/user/logout.php' : 'logout.php'; ?>"
                                    class="dropdown-item">
                                    <span class="material-symbols-outlined">logout</span>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="icon-btn" aria-label="Account">
                            <span class="material-symbols-outlined">person</span>
                        </a>
                    <?php endif; ?>

                    <a href="cart.php" class="icon-btn" aria-label="Cart">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </header>
        <main class="main-content">