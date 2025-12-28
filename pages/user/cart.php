<?php
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

$stmt = $pdo->prepare("SELECT id FROM shopping_carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

$cart_items = [];
$subtotal = 0;

if ($cart) {
    $cart_id = $cart['id'];

    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.description, p.price, p.image_url, p.category_id
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cart_items = $stmt->fetchAll();

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

                    <!-- Coupon Code Section -->
                    <div style="padding: 1rem 0; border-top: 1px solid #e5e7eb; margin: 1rem 0;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Have a Coupon?</h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="couponCode" placeholder="Enter coupon code"
                                style="flex: 1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                            <button type="button" class="btn btn-secondary" onclick="applyCoupon()">Apply</button>
                        </div>
                        <div id="couponMessage" style="margin-top: 0.5rem; font-size: 0.875rem;"></div>
                    </div>

                    <div id="discountRow" class="summary-row"
                        style="display:none; color: var(--color-primary); font-weight: 600;">
                        <span>Discount</span>
                        <span class="amount" id="discountAmount">-$0.00</span>
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

<style>
    .coupon-section {
        padding: 1.5rem 0;
        border-top: 1px solid var(--color-gray-200);
        border-bottom: 1px solid var(--color-gray-200);
        margin: 1rem 0;
    }

    .coupon-title {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .coupon-input-group {
        display: flex;
        gap: 0.5rem;
    }

    .coupon-input-group .form-input {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid var(--color-gray-300);
        border-radius: var(--radius-sm);
    }

    .coupon-message {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .coupon-message.success {
        color: var(--color-primary);
    }

    .coupon-message.error {
        color: #dc2626;
    }

    .discount-row {
        color: var(--color-primary);
        font-weight: 600;
    }
</style>

<script>
    function applyCoupon() {
        const couponCode = document.getElementById('couponCode').value.trim();
        const messageEl = document.getElementById('couponMessage');

        if (!couponCode) {
            messageEl.className = 'coupon-message error';
            messageEl.textContent = 'Please enter a coupon code';
            return;
        }

        // Call API to validate coupon
        fetch('../../api/coupon_validate.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `coupon_code=${encodeURIComponent(couponCode)}&order_total=<?php echo $subtotal; ?>`
        })
            .then(res => res.json())
            .then(data => {
                if (data.valid) {
                    messageEl.className = 'coupon-message success';
                    messageEl.textContent = `✓ Coupon applied! You save $${data.discount_amount}`;
                    document.getElementById('discountRow').style.display = 'flex';
                    document.getElementById('discountAmount').textContent = `-$${data.discount_amount}`;
                    const newTotal = <?php echo $total; ?> - parseFloat(data.discount_amount);
                    document.querySelector('.summary-total .amount').textContent = `$${newTotal.toFixed(2)}`;
                } else {
                    messageEl.className = 'coupon-message error';
                    messageEl.textContent = data.message || 'Invalid coupon code';
                    document.getElementById('discountRow').style.display = 'none';
                }
            })
            .catch(err => {
                messageEl.className = 'coupon-message error';
                messageEl.textContent = 'Error applying coupon';
                console.error(err);
            });
    }
</script>

<?php include("../../includes/footer.php"); ?>