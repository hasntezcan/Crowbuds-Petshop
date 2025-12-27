<?php
// DB connection via header
// But we need to set page title and assets path before header
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/product_detail.css";

include("../../includes/db_connect.php");

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($product_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch();
}

if (!$product) {
    // Redirect or show 404
    // For now, simpler:
    $page_title = "Product Not Found - Crawl Buds PetShop";
    include("../../includes/header.php");
    echo "<div style='padding:4rem; text-align:center;'><h1>Product not found.</h1><a href='home.php'>Back to Home</a></div>";
    include("../../includes/footer.php");
    exit;
}

$page_title = htmlspecialchars($product['name']) . " - Crawl Buds PetShop";
$active_page = "shop";
include("../../includes/header.php");

// Image Handling
$main_image = "../../" . htmlspecialchars($product['image_url']);
// Using the same image for thumbnails for simplicity as DB doesn't have valid gallery table
?>

<div class="page-container">
    <nav class="breadcrumb"><a href="home.php">Home</a> > <a href="home.php">Shop</a> >
        <?php echo htmlspecialchars($product['name']); ?></nav>

    <div class="product-layout">
        <!-- Gallery -->
        <section class="product-gallery">
            <div class="main-image" style="background-image: url('<?php echo $main_image; ?>');"></div>
            <div class="thumbnail-list">
                <!-- Placeholder logic for thumbnails -->
                <div class="thumbnail active" style="background-image: url('<?php echo $main_image; ?>');"></div>
            </div>
        </section>

        <!-- Product Info -->
        <section class="product-details">
            <div class="header-group">
                <span class="category-badge">Pet Supplies</span>
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="rating-container">
                    <div class="stars">
                        <span class="material-symbols-outlined filled">star</span>
                        <span class="material-symbols-outlined filled">star</span>
                        <span class="material-symbols-outlined filled">star</span>
                        <span class="material-symbols-outlined filled">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                </div>
            </div>

            <div class="product-description-list">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="price-stock-group">
                <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="stock-status in-stock">In Stock (<?php echo $product['stock_quantity']; ?> left)</span>
                <?php else: ?>
                    <span class="stock-status out-stock" style="color:var(--color-danger)">Out of Stock</span>
                <?php endif; ?>
            </div>

            <div class="purchase-actions">
                <form action="cart.php" method="POST" style="display:flex; gap:1rem; width:100%;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="quantity-selector">
                        <button type="button" class="qty-btn"
                            onclick="document.getElementById('qty').value--">-</button>
                        <input type="number" id="qty" name="quantity" value="1" class="qty-input" min="1"
                            max="<?php echo $product['stock_quantity']; ?>">
                        <button type="button" class="qty-btn"
                            onclick="document.getElementById('qty').value++">+</button>
                    </div>

                    <button type="submit" class="btn btn-primary btn-add-cart" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                        <span class="material-symbols-outlined">add_shopping_cart</span>
                        Add to Cart
                    </button>
                </form>
            </div>

            <div class="shipping-info">
                <div class="info-item">
                    <span class="material-symbols-outlined">local_shipping</span>
                    <p>Estimated delivery: <strong>3-5 business days</strong></p>
                </div>
            </div>
        </section>
    </div>

    <!-- Related Products -->
    <section class="related-products">
        <h3 class="section-title">You might also like</h3>
        <div class="product-grid">
            <?php
            // Fetch random related products
            $stmt_rel = $pdo->prepare("SELECT * FROM products WHERE id != :id AND is_active = 1 LIMIT 4");
            $stmt_rel->execute(['id' => $product['id']]);
            $related = $stmt_rel->fetchAll();
            foreach ($related as $rel):
                $rel_img = "../../" . htmlspecialchars($rel['image_url']);
                ?>
                <article class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo $rel_img; ?>');"></div>
                    <div class="product-info">
                        <h4 class="product-title"><a
                                href="product_detail.php?id=<?php echo $rel['id']; ?>"><?php echo htmlspecialchars($rel['name']); ?></a>
                        </h4>
                        <span class="product-price">$<?php echo number_format($rel['price'], 2); ?></span>
                    </div>
                    <a href="product_detail.php?id=<?php echo $rel['id']; ?>" class="btn btn-secondary btn-full">View</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include("../../includes/footer.php"); ?>