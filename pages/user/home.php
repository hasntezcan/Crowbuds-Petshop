<?php
$page_title = "Home - Crawl Buds PetShop";
$active_page = "home";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/home.css";
include("../../includes/header.php");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Everything your pet needs, in one place.</h1>
        <p class="hero-subtitle">Find the best food, toys, and accessories to keep your furry, scaly, or feathery
            friends happy and healthy.</p>
        <div class="hero-actions">
            <a href="shop.php" class="btn btn-primary btn-lg">Shop Now</a>
            <a href="shop.php#products" class="btn btn-ghost btn-lg">Browse Products</a>
        </div>
    </div>
    <div class="hero-image" style="background-image: url('../../assets/images/hero_pets.png');"></div>
</section>

<!-- Categories Showcase -->
<section class="categories-section">
    <div class="section-header">
        <h2>Shop by Category</h2>
        <p>Find exactly what your pet needs</p>
    </div>
    <div class="categories-grid">
        <?php
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT * FROM categories LIMIT 6");
            $categories = $stmt->fetchAll();
            foreach ($categories as $cat):
                ?>
                <a href="shop.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <div class="category-icon">
                        <span class="material-symbols-outlined">pets</span>
                    </div>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p><?php echo htmlspecialchars($cat['description']); ?></p>
                </a>
            <?php endforeach;
        } ?>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-section">
    <div class="section-header">
        <h2>Featured Products</h2>
        <p>Handpicked favorites for your companion</p>
    </div>
    <div class="product-grid">
        <?php
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY stock_quantity DESC LIMIT 4");
            $products = $stmt->fetchAll();

            foreach ($products as $product):
                $image_path = "../../" . htmlspecialchars($product['image_url']);
                ?>
                <article class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo $image_path; ?>');"></div>
                    <div class="product-info">
                        <h4 class="product-title">
                            <a
                                href="product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                        </h4>
                        <p class="product-desc"><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?>
                        </p>
                        <div class="product-meta">
                            <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="status-badge status-instock">In Stock</span>
                            <?php else: ?>
                                <span class="status-badge status-outstock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-secondary btn-full" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                            Add to Cart
                        </button>
                    </form>
                </article>
            <?php endforeach;
        } ?>
    </div>
    <div class="section-footer">
        <a href="shop.php" class="btn btn-primary">View All Products</a>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
    <div class="benefit-card">
        <span class="material-symbols-outlined">local_shipping</span>
        <h3>Fast Shipping</h3>
        <p>Free delivery on orders over $50</p>
    </div>
    <div class="benefit-card">
        <span class="material-symbols-outlined">verified</span>
        <h3>Quality Guaranteed</h3>
        <p>100% authentic products</p>
    </div>
    <div class="benefit-card">
        <span class="material-symbols-outlined">support_agent</span>
        <h3>24/7 Support</h3>
        <p>We're here to help anytime</p>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>