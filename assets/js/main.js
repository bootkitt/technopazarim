// Main JavaScript for TechnoPazarim

// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme based on saved preference or system preference
    initializeTheme();
    
    // Add to cart functionality for buttons with data-product-id
    const addToCartButtons = document.querySelectorAll('.add-to-cart[data-product-id]');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            // If there's a quantity input in the same container, use its value
            let quantity = 1;
            const quantityInput = this.closest('.flex').querySelector('input[type="number"]#quantity');
            if (quantityInput) {
                quantity = parseInt(quantityInput.value) || 1;
            }
            
            addToCart(productId, quantity);
        });
    });
    
    // Remove from cart functionality
    const removeFromCartButtons = document.querySelectorAll('.remove-from-cart');
    removeFromCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });
    
    // Update cart quantity functionality
    const updateQuantityInputs = document.querySelectorAll('.update-quantity');
    updateQuantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value) || 1;
            updateCartQuantity(productId, quantity);
        });
    });
    
    // Quantity selector functionality for product pages
    const decreaseButtons = document.querySelectorAll('.decrease-quantity');
    const increaseButtons = document.querySelectorAll('.increase-quantity');
    const quantityInputs = document.querySelectorAll('input[type="number"]#quantity');
    
    decreaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const container = this.closest('.flex');
            const input = container.querySelector('input[type="number"]#quantity');
            if (input) {
                let value = parseInt(input.value) || 1;
                if (value > 1) {
                    input.value = value - 1;
                }
            }
        });
    });
    
    increaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const container = this.closest('.flex');
            const input = container.querySelector('input[type="number"]#quantity');
            if (input) {
                let value = parseInt(input.value) || 1;
                const max = parseInt(input.max) || 999;
                if (value < max) {
                    input.value = value + 1;
                }
            }
        });
    });
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            let value = parseInt(this.value) || 1;
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
        });
    });
});

// Theme functions
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // If no saved theme, use system preference
    const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
    
    document.documentElement.setAttribute('data-theme', theme);
    
    // Listen for system theme changes if no saved preference
    if (!savedTheme) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
        });
    }
}

// Cart functions
function addToCart(productId, quantity = 1) {
    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showMessage('Ürün sepete eklendi', 'success');
        } else {
            showMessage(data.message || 'Bir hata oluştu', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu', 'danger');
    });
}

function removeFromCart(productId) {
    fetch('ajax/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to update the cart display
            location.reload();
        } else {
            showMessage(data.message || 'Bir hata oluştu', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu', 'danger');
    });
}

function updateCartQuantity(productId, quantity) {
    fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to update the cart display
            location.reload();
        } else {
            showMessage(data.message || 'Bir hata oluştu', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu', 'danger');
    });
}

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        if (count > 0) {
            element.textContent = count;
            element.style.display = 'flex';
        } else {
            element.style.display = 'none';
        }
    });
}

// Show message function
function showMessage(message, type) {
    // Remove any existing alerts
    const existingAlert = document.querySelector('.alert-fixed');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'danger' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    
    alert.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">${message}</div>
            <button type="button" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add close functionality
    alert.querySelector('button').addEventListener('click', () => {
        alert.remove();
    });
    
    // Add to document
    document.body.appendChild(alert);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 3000);
}