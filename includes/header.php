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
    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?php echo $extra_css; ?>" />
    <?php endif; ?>
</head>

<body>
    <div class="app-container">
        <!-- TopNavBar -->
        <header class="main-header">
            <div class="header-container">
                <div class="brand-section">
                    <div class="brand-logo">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M44 4H30.6666V17.3334H17.3334V30.6666H4V44H44V4Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <h2 class="brand-title">Crawl Buds PetShop</h2>
                </div>

                <nav class="main-nav">
                    <a href="home.php" class="nav-link <?php echo ($active_page == 'home') ? 'active' : ''; ?>">Home</a>
                    <a href="shop.php" class="nav-link <?php echo ($active_page == 'shop') ? 'active' : ''; ?>">Shop</a>
                    <a href="my_orders.php"
                        class="nav-link <?php echo ($active_page == 'orders') ? 'active' : ''; ?>">My Orders</a>
                    <a href="#" class="nav-link">Help</a>
                </nav>

                <div class="header-actions">
                    <?php if ($is_logged_in): ?>
                        <span class="user-greeting">Hello, <?php echo htmlspecialchars($user_name); ?>!</span>
                        <a href="logout.php" class="nav-link">Logout</a>
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