<?php
// Require user login
include_once("../../includes/user_guard.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Shopping Cart - Crawl Buds PetShop";
$active_page = "cart";
$assets_path = "../../assets";
$extra_css = $assets_path . "/css/pages/cart.css";

include("../../includes/db_connect.php");

$user_id = $_SESSION['user_id'];

// Get user's cart from database
$stmt = $pdo->prepare("SELECT id FROM shopping_carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

$cart_items = [];
$subtotal = 0;

if ($cart) {
    $cart_id = $cart['id'];

    // Get cart items with product details
    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.description, p.price, p.image_url, p.category_id
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cart_items = $stmt->fetchAll();

    // Calculate totals
    foreach ($cart_items as $item) {
        $subtotal += $item['item_total'];
    }
}

$shipping = ($subtotal > 50 || $subtotal == 0) ? 0 : 5.00;
$total = $subtotal + $shipping;

include("../../includes/header.php");
?>

<div class="page-container">
    <div class="cart-header">
        <nav class="breadcrumb">
            <a href="cart.php" class="current">Cart</a>
            <span class="separator">/</span>
            <span class="disabled">Checkout</span>
            <span class="separator">/</span>
            <span class="disabled">Order Complete</span>
        </nav>

        <div class="page-heading">
            <h1 class="page-title">My Cart (<?php echo count($cart_items); ?> Items)</h1>
            <a href="shop.php" class="btn btn-secondary btn-sm">Continue Shopping</a>
        </div>
    </div>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 4rem;">
            <h2>Your cart is empty.</h2>
            <a href="shop.php" class="btn btn-primary" style="margin-top: 1rem;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <!-- Cart Items List -->
            <div class="cart-items-column">
                <div class="cart-card">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="col-product">Product</th>
                                <th class="col-price">Price</th>
                                <th class="col-qty">Quantity</th>
                                <th class="col-total">Subtotal</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item):
                                $img = "../../" . htmlspecialchars($item['image_url']);
                                ?>
                                <tr>
                                    <td class="col-product">
                                        <div class="item-details">
                                            <div class="item-image" style="background-image: url('<?php echo $img; ?>');"></div>
                                            <div class="item-info">
                                                <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                                <span class="item-sub">Category ID: <?php echo $item['category_id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-price">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="col-qty">
                                        <div class="qty-control">
                                            <button class="qty-btn"
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                            <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                            <button class="qty-btn"
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                                        </div>
                                    </td>
                                    <td class="col-total">$<?php echo number_format($item['item_total'], 2); ?></td>
                                    <td class="col-action">
                                        <button class="delete-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary-column">
                <div class="summary-card sticky-summary">
                    <h2 class="summary-title">Order Summary</h2>

                    <div class="summary-rows">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span class="amount">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Estimated Shipping</span>
                            <span class="amount">$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="amount">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary btn-full btn-lg">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .qty-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .qty-btn {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--color-gray-300);
        background: white;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .qty-value {
        min-width: 30px;
        text-align: center;
        font-weight: 600;
    }

    .delete-btn {
        background: none;
        border: none;
        color: var(--color-gray-500);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .delete-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }
</style>

<script>
    function updateQuantity(itemId, newQty) {
        if (newQty < 1) {
            if (!confirm('Remove this item from cart?')) return;
            removeItem(itemId);
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('item_id', itemId);
        formData.append('quantity', newQty);

        fetch('../../api/cart_operations.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Notify.error(data.message || 'Failed to update quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Notify.error('Network error');
            });
    }

    function removeItem(itemId) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('item_id', itemId);

        fetch('../../api/cart_operations.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Notify.success('Item removed from cart');
                    setTimeout(() => location.reload(), 500);
                } else {
                    Notify.error(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Notify.error('Network error');
            });
    }
</script>

<?php include("../../includes/footer.php"); ?>