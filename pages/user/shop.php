<?php
$page_title = "Shop - Crawl Buds PetShop";
$active_page = "shop";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/shop.css";
include("../../includes/header.php");

// Get categories for filter
$categories = [];
if (isset($pdo)) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Shop All Products</h1>
    <p>Browse our complete collection of pet supplies</p>
</div>

<!-- Main Layout -->
<div class="page-layout" id="products">
    <!-- Filters Sidebar -->
    <aside class="sidebar">
        <div class="sticky-wrapper">
            <h3 class="sidebar-title">Filters</h3>

            <div class="accordion-group">
                <!-- Category Filter -->
                <details class="accordion" open>
                    <summary class="accordion-header">
                        <span>Category</span>
                        <span class="material-symbols-outlined">expand_more</span>
                    </summary>
                    <div class="accordion-content">
                        <?php foreach ($categories as $cat): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" class="filter-category" value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <!-- Price Range Filter -->
                <details class="accordion">
                    <summary class="accordion-header">
                        <span>Price Range</span>
                        <span class="material-symbols-outlined">expand_more</span>
                    </summary>
                    <div class="accordion-content">
                        <div class="price-inputs">
                            <input type="number" id="min-price" placeholder="Min" value="0" class="form-input">
                            <span>to</span>
                            <input type="number" id="max-price" placeholder="Max" value="1000" class="form-input">
                        </div>
                    </div>
                </details>

                <!-- Availability Filter -->
                <details class="accordion">
                    <summary class="accordion-header">
                        <span>Availability</span>
                        <span class="material-symbols-outlined">expand_more</span>
                    </summary>
                    <div class="accordion-content">
                        <label class="checkbox-label">
                            <input type="checkbox" id="filter-in-stock"> In Stock Only
                        </label>
                    </div>
                </details>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary btn-full" id="apply-filters">Apply Filters</button>
                <button class="btn btn-ghost btn-full" id="clear-filters">Clear</button>
            </div>
        </div>
    </aside>

    <!-- Product Grid Section -->
    <section class="content-area">
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-bar">
                <span class="material-symbols-outlined search-icon">search</span>
                <input type="search" id="product-search" placeholder="Search for pet products..." class="search-input">
            </div>
            <div class="sort-dropdown">
                <select class="select-input" id="sort-select">
                    <option value="popularity">Sort by: Popularity</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="newest">Newest</option>
                    <option value="name">Name (A-Z)</option>
                </select>
            </div>
        </div>

        <!-- Products will be loaded here by JavaScript -->
        <div class="product-grid" id="product-grid">
            <p>Loading products...</p>
        </div>

        <!-- Pagination -->
        <nav class="pagination" id="pagination">
            <!-- Will be populated by JavaScript -->
        </nav>
    </section>
</div>

<script src="../../assets/js/shop.js"></script>
<?php include("../../includes/footer.php"); ?>