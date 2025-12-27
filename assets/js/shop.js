'use strict';

let currentFilters = {
    search: '',
    categories: [],
    min_price: 0,
    max_price: 999999,
    in_stock: false,
    sort: 'popularity',
    page: 1
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    loadProducts();

    // Search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('product-search');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = e.target.value;
                currentFilters.page = 1;
                loadProducts();
            }, 500);
        });
    }

    // Sort change
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function (e) {
            currentFilters.sort = e.target.value;
            currentFilters.page = 1;
            loadProducts();
        });
    }

    // Apply filters button
    const applyBtn = document.getElementById('apply-filters');
    if (applyBtn) {
        applyBtn.addEventListener('click', applyFilters);
    }

    // Clear filters button
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearFilters);
    }
});

function applyFilters() {
    // Get selected categories
    const categoryCheckboxes = document.querySelectorAll('.filter-category:checked');
    currentFilters.categories = Array.from(categoryCheckboxes).map(cb => cb.value);

    // Get price range
    const minPrice = document.getElementById('min-price');
    const maxPrice = document.getElementById('max-price');
    currentFilters.min_price = minPrice ? (minPrice.value || 0) : 0;
    currentFilters.max_price = maxPrice ? (maxPrice.value || 999999) : 999999;

    // Get in stock filter
    const inStockCheckbox = document.getElementById('filter-in-stock');
    currentFilters.in_stock = inStockCheckbox ? inStockCheckbox.checked : false;

    currentFilters.page = 1;
    loadProducts();
}

function clearFilters() {
    // Reset checkboxes
    document.querySelectorAll('.filter-category').forEach(cb => cb.checked = false);
    const inStockCheckbox = document.getElementById('filter-in-stock');
    if (inStockCheckbox) inStockCheckbox.checked = false;

    // Reset price inputs
    const minPrice = document.getElementById('min-price');
    const maxPrice = document.getElementById('max-price');
    if (minPrice) minPrice.value = 0;
    if (maxPrice) maxPrice.value = 1000;

    // Reset search
    const searchInput = document.getElementById('product-search');
    if (searchInput) searchInput.value = '';

    // Reset filters object
    currentFilters = {
        search: '',
        categories: [],
        min_price: 0,
        max_price: 999999,
        in_stock: false,
        sort: 'popularity',
        page: 1
    };

    loadProducts();
}

function loadProducts() {
    const grid = document.getElementById('product-grid');
    if (!grid) return;

    grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">Loading products...</p>';

    // Build query string
    const params = new URLSearchParams();
    params.append('search', currentFilters.search);
    if (currentFilters.categories.length > 0) {
        params.append('category', currentFilters.categories[0]);
    }
    params.append('min_price', currentFilters.min_price);
    params.append('max_price', currentFilters.max_price);
    params.append('in_stock', currentFilters.in_stock);
    params.append('sort', currentFilters.sort);
    params.append('page', currentFilters.page);

    // Fetch products
    fetch(`../../api/product_search.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
                displayPagination(data.page, data.total_pages);
            } else {
                grid.innerHTML = '<p style="grid-column: 1/-1;">Error loading products</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            grid.innerHTML = '<p style="grid-column: 1/-1;">Error loading products</p>';
        });
}

function displayProducts(products) {
    const grid = document.getElementById('product-grid');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">No products found</p>';
        return;
    }

    grid.innerHTML = products.map(product => {
        const imagePath = '../../' + product.image_url;
        const inStock = product.stock_quantity > 0;
        const stockBadge = inStock
            ? '<span class="status-badge status-instock">In Stock</span>'
            : '<span class="status-badge status-outstock">Out of Stock</span>';

        return `
            <article class="product-card">
                <div class="product-image" style="background-image: url('${imagePath}');"></div>
                <div class="product-info">
                    <h4 class="product-title">
                        <a href="product_detail.php?id=${product.id}">${escapeHtml(product.name)}</a>
                    </h4>
                    <p class="product-desc">${escapeHtml(product.description.substring(0, 50))}...</p>
                    <div class="product-meta">
                        <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                        ${stockBadge}
                    </div>
                </div>
                <div class="add-to-cart-wrapper">
                    <button 
                        class="btn btn-primary btn-full add-to-cart-btn" 
                        data-product-id="${product.id}"
                        ${!inStock ? 'disabled' : ''}
                    >
                        Add to Cart
                    </button>
                </div>
            </article>
        `;
    }).join('');

    // Attach event listeners to add-to-cart buttons
    grid.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            addToCart(this.dataset.productId);
        });
    });
}

function displayPagination(currentPage, totalPages) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    if (currentPage > 1) {
        html += `<a href="#" class="page-link" data-page="${currentPage - 1}">Previous</a>`;
    }

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            html += `<a href="#" class="page-link active">${i}</a>`;
        } else if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
            html += `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="page-ellipsis">...</span>`;
        }
    }

    // Next button
    if (currentPage < totalPages) {
        html += `<a href="#" class="page-link" data-page="${currentPage + 1}">Next</a>`;
    }

    pagination.innerHTML = html;

    // Attach click handlers
    pagination.querySelectorAll('.page-link[data-page]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            currentFilters.page = parseInt(this.dataset.page);
            loadProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
}

function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    fetch('../../api/cart_operations.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Notify.success('Product added to your cart!');
                updateCartCount();
            } else {
                Notify.error(data.message || 'Failed to add product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Notify.error('Network error. Please try again.');
        });
}

function updateCartCount() {
    fetch('../../api/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.header-actions .badge');
                if (badge) {
                    badge.textContent = data.count;
                }
            }
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
