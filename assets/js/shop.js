let currentFilters = {
    search: '',
    category: [],
    minPrice: '',
    maxPrice: '',
    sort: 'newest',
    page: 1
};

document.addEventListener('DOMContentLoaded', function () {
    initializeFilters();
    loadProducts();
});

function initializeFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category');
    if (categoryId) {
        currentFilters.category = [categoryId];
        const checkbox = document.querySelector(`input[name="category"][value="${categoryId}"]`);
        if (checkbox) checkbox.checked = true;
    }

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            currentFilters.search = this.value;
            currentFilters.page = 1;
            loadProducts();
        }, 500));
    }

    document.querySelectorAll('input[name="category"]').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            currentFilters.category = Array.from(
                document.querySelectorAll('input[name="category"]:checked')
            ).map(cb => cb.value);
            currentFilters.page = 1;
            loadProducts();
        });
    });

    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    if (minPrice && maxPrice) {
        minPrice.addEventListener('change', function () {
            currentFilters.minPrice = this.value;
            currentFilters.page = 1;
            loadProducts();
        });
        maxPrice.addEventListener('change', function () {
            currentFilters.maxPrice = this.value;
            currentFilters.page = 1;
            loadProducts();
        });
    }

    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            currentFilters.sort = this.value;
            currentFilters.page = 1;
            loadProducts();
        });
    }
}

function loadProducts() {
    const grid = document.getElementById('productsContainer');
    if (!grid) return;

    grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding:3rem;">Loading products...</p>';

    const params = new URLSearchParams();
    if (currentFilters.search) params.append('search', currentFilters.search);
    if (currentFilters.category.length > 0) params.append('category', currentFilters.category.join(','));
    if (currentFilters.minPrice) params.append('min_price', currentFilters.minPrice);
    if (currentFilters.maxPrice) params.append('max_price', currentFilters.maxPrice);
    if (currentFilters.sort) params.append('sort', currentFilters.sort);
    params.append('page', currentFilters.page);

    fetch(`../../api/product_search.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.products);
                displayPagination(data.current_page, data.total_pages);
            } else {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding:3rem;">Error loading products.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding:3rem;">Network error. Please try again.</p>';
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

    if (currentPage > 1) {
        html += `<a href="#" class="page-link" data-page="${currentPage - 1}">Previous</a>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            html += `<a href="#" class="page-link active">${i}</a>`;
        } else if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
            html += `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="page-ellipsis">...</span>`;
        }
    }

    if (currentPage < totalPages) {
        html += `<a href="#" class="page-link" data-page="${currentPage + 1}">Next</a>`;
    }

    pagination.innerHTML = html;

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

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
function renderProducts(products) {
    const grid = document.getElementById('productsContainer');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding:3rem;">No products found.</p>';
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
                    <div class="product-meta">
                        <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                        ${stockBadge}
                    </div>
                </div>
                <div class="product-card-footer">
                    <button 
                        class="btn btn-secondary btn-full add-to-cart-btn" 
                        data-product-id="${product.id}"
                        ${!inStock ? 'disabled' : ''}
                    >
                        Add to Cart
                    </button>
                </div>
            </article>
        `;
    }).join('');

    grid.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            addToCart(this.dataset.productId);
        });
    });
}
