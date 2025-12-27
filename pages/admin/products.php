<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Product Management";
$active_page = "products";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $name = trim($_POST['name']);
        $category_id = (int) $_POST['category_id'];
        $description = trim($_POST['description']);
        $price = (float) $_POST['price'];
        $stock = (int) $_POST['stock_quantity'];

        // Handle image upload
        $image_path = 'assets/images/product_default.png';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../../assets/images/';
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'assets/images/' . $new_filename;
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock_quantity, image_url, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$category_id, $name, $description, $price, $stock, $image_path]);
            $success = "Product added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding product: " . $e->getMessage();
        }
    } elseif ($action == 'edit') {
        $id = (int) $_POST['product_id'];
        $name = trim($_POST['name']);
        $category_id = (int) $_POST['category_id'];
        $description = trim($_POST['description']);
        $price = (float) $_POST['price'];
        $stock = (int) $_POST['stock_quantity'];

        // Get current image
        $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        $image_path = $current['image_url'];

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../../assets/images/';
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'assets/images/' . $new_filename;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock_quantity = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$category_id, $name, $description, $price, $stock, $image_path, $id]);
            $success = "Product updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
        }
    } elseif ($action == 'delete') {
        $id = (int) $_POST['product_id'];
        try {
            // Soft delete - set is_active to 0
            $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Product deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting product: " . $e->getMessage();
        }
    } elseif ($action == 'toggle_active') {
        $id = (int) $_POST['product_id'];
        $is_active = (int) $_POST['is_active'];
        try {
            $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            $stmt->execute([$is_active, $id]);
            $success = "Product status updated!";
        } catch (PDOException $e) {
            $error = "Error updating status: " . $e->getMessage();
        }
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$products = $stmt->fetchAll();

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-page-header">
    <h1>Manage Products</h1>
    <button class="btn btn-primary" onclick="showAddProductModal()">+ Add New Product</button>
</div>

<div class="section-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <img src="../../<?php echo $product['image_url']; ?>" alt="Product"
                                style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['stock_quantity'] < 5): ?>
                                <span class="badge badge-red"><?php echo $product['stock_quantity']; ?></span>
                            <?php else: ?>
                                <?php echo $product['stock_quantity']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="is_active" value="<?php echo $product['is_active'] ? 0 : 1; ?>">
                                <button type="submit"
                                    class="badge <?php echo $product['is_active'] ? 'badge-green' : 'badge-gray'; ?>"
                                    style="border:none;cursor:pointer;">
                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-secondary"
                                onclick='editProduct(<?php echo json_encode($product); ?>)'>Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('addProductModal')">&times;</span>
        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" class="form-input" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" class="form-input" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-input" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price ($) *</label>
                    <input type="number" step="0.01" name="price" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('editProductModal')">&times;</span>
        <h2>Edit Product</h2>
        <form method="POST" enctype="multipart/form-data" id="editProductForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="product_id" id="edit_product_id">

            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" id="edit_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" id="edit_category" class="form-input" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" class="form-input" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price ($) *</label>
                    <input type="number" step="0.01" name="price" id="edit_price" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" id="edit_stock" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label>Change Image (optional)</label>
                <input type="file" name="image" accept="image/*" class="form-input">
                <small>Leave empty to keep current image</small>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</div>

<script>
    function showAddProductModal() {
        document.getElementById('addProductModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function editProduct(product) {
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_category').value = product.category_id;
        document.getElementById('edit_description').value = product.description;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_stock').value = product.stock_quantity;

        document.getElementById('editProductModal').style.display = 'flex';
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-close {
        float: right;
        font-size: 2rem;
        cursor: pointer;
        color: #999;
    }

    .modal-close:hover {
        color: #333;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
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

    .admin-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>

<?php include("../../includes/admin_footer.php"); ?>