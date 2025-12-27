<?php
if (!isset($page_title)) {
    $page_title = "Admin - Crawl Buds PetShop";
}
if (!isset($active_page)) {
    $active_page = "";
}
if (!isset($assets_path)) {
    $assets_path = "../../assets";
}
if (!isset($extra_css)) {
    $extra_css = "";
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/style.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/admin.css" />
    <?php if ($extra_css): ?>
        <link rel="stylesheet" href="<?php echo $extra_css; ?>" />
    <?php endif; ?>
</head>

<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon"
                        style="background-image: url('<?php echo $assets_path; ?>/images/logo_placeholder.png');"></div>
                    <!-- Using placeholder or CSS logic -->
                    <div class="logo-text">
                        <h1>Crawl Buds</h1>
                        <span>PetShop Admin</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-group">
                    <a href="dashboard.php"
                        class="nav-item <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="products.php" class="nav-item <?php echo $active_page === 'products' ? 'active' : ''; ?>">
                        <span class="material-symbols-outlined">inventory_2</span>
                        <span>Products</span>
                    </a>
                    <a href="orders.php" class="nav-item <?php echo $active_page === 'orders' ? 'active' : ''; ?>">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span>Orders</span>
                    </a>
                    <a href="customers.php"
                        class="nav-item <?php echo $active_page === 'customers' ? 'active' : ''; ?>">
                        <span class="material-symbols-outlined">group</span>
                        <span>Customers</span>
                    </a>
                    <a href="coupons.php" class="nav-item <?php echo $active_page === 'coupons' ? 'active' : ''; ?>">
                        <span class="material-symbols-outlined">local_offer</span>
                        <span>Coupons</span>
                    </a>
                </div>

                <div class="nav-group">
                    <a href="#" class="nav-item">
                        <span class="material-symbols-outlined">settings</span>
                        <span>Settings</span>
                    </a>
                    <a href="../user/logout.php" class="nav-item">
                        <span class="material-symbols-outlined">logout</span>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="admin-main">
            <!-- Top Header -->
            <header class="admin-top-header">
                <h2 class="header-title"><?php echo htmlspecialchars($page_title); ?></h2>
                <div class="header-actions">
                    <button class="user-menu-btn">
                        <span><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                    <div class="user-avatar"
                        style="background-image: url('<?php echo $assets_path; ?>/images/admin_avatar.png');"></div>
                </div>
            </header>

            <div class="admin-content">