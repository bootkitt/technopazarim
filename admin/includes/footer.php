        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center space-x-4">
                <span>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></span>
                <span>•</span>
                <span>Admin Panel v2.0</span>
            </div>
            <div class="flex items-center space-x-4">
                <span>PHP <?php echo PHP_VERSION; ?></span>
                <span>•</span>
                <span>
                    <i class="fas fa-clock mr-1"></i>
                    <?php echo date('H:i'); ?>
                </span>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-8 shadow-2xl">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Yükleniyor...</span>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 shadow-2xl max-w-md mx-4">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Emin misiniz?</h3>
                <p class="text-gray-600 mb-6" id="confirm-message">Bu işlemi gerçekleştirmek istediğinizden emin misiniz?</p>
                <div class="flex space-x-4">
                    <button id="confirm-cancel" class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        İptal
                    </button>
                    <button id="confirm-ok" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Onayla
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toast notification system
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform translate-x-full opacity-0 transition-all duration-300`;
            toast.innerHTML = `
                <i class="fas ${icons[type]}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        
        // Loading overlay
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }
        
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }
        
        // Confirmation modal
        function showConfirm(message, callback) {
            const modal = document.getElementById('confirm-modal');
            const messageEl = document.getElementById('confirm-message');
            const cancelBtn = document.getElementById('confirm-cancel');
            const okBtn = document.getElementById('confirm-ok');
            
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            
            cancelBtn.onclick = () => {
                modal.classList.add('hidden');
            };
            
            okBtn.onclick = () => {
                modal.classList.add('hidden');
                callback();
            };
        }
        
        // AJAX helper
        function ajaxRequest(url, data = null, method = 'GET') {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data ? JSON.stringify(data) : null
            }).then(response => response.json());
        }
        
        // Auto-refresh dashboard stats
        function refreshDashboardStats() {
            if (window.location.pathname.includes('index.php')) {
                ajaxRequest('ajax/dashboard-stats.php')
                    .then(data => {
                        if (data.success) {
                            // Update stat cards
                            Object.keys(data.stats).forEach(key => {
                                const element = document.querySelector(`[data-stat="${key}"]`);
                                if (element) {
                                    element.textContent = data.stats[key];
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Stats refresh error:', error));
            }
        }
        
        // Initialize auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            // Refresh stats every 30 seconds
            setInterval(refreshDashboardStats, 30000);
            
            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
        });
        
        // Handle form submissions with loading states
        document.addEventListener('submit', function(event) {
            const form = event.target;
            if (form.classList.contains('ajax-form')) {
                event.preventDefault();
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>İşleniyor...';
                
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                ajaxRequest(form.action, data, form.method)
                    .then(response => {
                        if (response.success) {
                            showToast(response.message, 'success');
                            if (response.redirect) {
                                setTimeout(() => {
                                    window.location.href = response.redirect;
                                }, 1000);
                            }
                        } else {
                            showToast(response.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Bir hata oluştu', 'error');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            }
        });
        
        // Data tables enhancement
        function initDataTable(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            // Add search functionality
            const searchInput = table.parentElement.querySelector('.table-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }
            
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function() {
                    const column = this.dataset.sort;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const isAsc = this.classList.contains('sort-asc');
                    
                    // Remove sort classes from all headers
                    headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
                    
                    // Add sort class to current header
                    this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
                    
                    // Sort rows
                    rows.sort((a, b) => {
                        const aVal = a.querySelector(`td[data-sort="${column}"]`).textContent;
                        const bVal = b.querySelector(`td[data-sort="${column}"]`).textContent;
                        
                        if (isAsc) {
                            return bVal.localeCompare(aVal, undefined, {numeric: true});
                        } else {
                            return aVal.localeCompare(bVal, undefined, {numeric: true});
                        }
                    });
                    
                    // Re-append sorted rows
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        }
        
        // Initialize data tables on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('table[data-table]').forEach(table => {
                initDataTable(table.id);
            });
        });
    </script>
</body>
</html>