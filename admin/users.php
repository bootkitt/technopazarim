<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login');
    exit;
}

// Helper functions for users.php
function formatMoney($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}

function logActivity($db, $userId, $type, $description, $adminId) {
    logSecurityEvent($adminId, $type, $description, $db);
}

$page_title = 'Kullanıcı Yönetimi';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'toggle_status':
            $userId = (int)$_POST['user_id'];
            $currentStatus = $_POST['current_status'];
            $newStatus = $currentStatus === 'active' ? 'banned' : 'active';
            
            $stmt = $db->prepare("UPDATE uyeler SET uye_onay = ? WHERE uye_id = ?");
            $statusValue = ($newStatus === 'active') ? 1 : 0;
            if ($stmt->execute([$statusValue, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Kullanıcı durumu güncellendi';
                logActivity($db, null, 'user_status_change', "User $userId status changed to $newStatus", $_SESSION['user_id']);
            }
            break;
            
        case 'update_balance':
            // This functionality doesn't exist in the current database schema
            $response['message'] = 'Bu özellik şu anda kullanılamıyor';
            break;
            
        case 'delete_user':
            $userId = (int)$_POST['user_id'];
            
            // In this schema, we'll just set the user as inactive instead of deleting
            $stmt = $db->prepare("UPDATE uyeler SET uye_onay = 0 WHERE uye_id = ?");
            if ($stmt->execute([$userId])) {
                $response['success'] = true;
                $response['message'] = 'Kullanıcı devre dışı bırakıldı';
                logActivity($db, null, 'user_delete', "User $userId disabled by admin", $_SESSION['user_id']);
            }
            break;
            
        case 'edit_user':
            $userId = (int)$_POST['user_id'];
            $name = clean($_POST['name']);
            $email = clean($_POST['email']);
            
            // Check if email already exists for another user
            $stmt = $db->prepare("SELECT uye_id FROM uyeler WHERE uye_eposta = ? AND uye_id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $response['message'] = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor';
                break;
            }
            
            $stmt = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_eposta = ? WHERE uye_id = ?");
            if ($stmt->execute([$name, $email, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Kullanıcı bilgileri güncellendi';
                logActivity($db, null, 'user_edit', "User $userId information updated", $_SESSION['user_id']);
            }
            break;
            
        case 'toggle_2fa':
            $userId = (int)$_POST['user_id'];
            $currentStatus = (int)$_POST['current_status'];
            $newStatus = $currentStatus === 1 ? 0 : 1;
            
            $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_enabled = ? WHERE uye_id = ?");
            if ($stmt->execute([$newStatus, $userId])) {
                $response['success'] = true;
                $response['message'] = '2FA durumu güncellendi';
                $response['new_status'] = $newStatus;
                logActivity($db, null, 'user_2fa_toggle', "User $userId 2FA toggled to $newStatus", $_SESSION['user_id']);
            }
            break;
    }
    
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Get users with pagination
$page = (int)($_GET['page'] ?? 1);
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = clean($_GET['search'] ?? '');
$status = clean($_GET['status'] ?? '');

$whereClause = [];
$params = [];

if ($search) {
    $whereClause[] = "(uye_adi LIKE ? OR uye_eposta LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $statusValue = ($status === 'active') ? 1 : 0;
    $whereClause[] = "uye_onay = ?";
    $params[] = $statusValue;
}

$whereSQL = $whereClause ? 'WHERE ' . implode(' AND ', $whereClause) : '';

// Get total count
$countQuery = "SELECT COUNT(*) FROM uyeler $whereSQL";
$totalUsers = $db->prepare($countQuery);
$totalUsers->execute($params);
$totalUsers = $totalUsers->fetchColumn();

$totalPages = ceil($totalUsers / $perPage);

// Get users
$usersQuery = "
    SELECT u.* 
    FROM uyeler u 
    $whereSQL 
    ORDER BY u.uye_tarih DESC 
    LIMIT $offset, $perPage
";
$stmt = $db->prepare($usersQuery);
$stmt->execute($params);
$users = $stmt->fetchAll();

include_once __DIR__ . '/includes/header.php';
?>

<!-- Users Management Content -->
<div class="flex-1 overflow-auto bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kullanıcı Yönetimi</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Toplam <?php echo number_format($totalUsers); ?> kullanıcı
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="exportUsers()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Excel'e Aktar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo clean($search); ?>" 
                               placeholder="Kullanıcı ara (ad, email)" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tüm Durumlar</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="banned" <?php echo $status === 'banned' ? 'selected' : ''; ?>>Yasaklı</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrele
                </button>
                
                <?php if ($search || $status): ?>
                <a href="users.php" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Temizle
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" data-table id="users-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th data-sort="username" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Kullanıcı
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th data-sort="email" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Email
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th data-sort="status" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Durum
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                2FA Durumu
                            </th>
                            <th data-sort="created_at" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Kayıt Tarihi
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td data-sort="username" class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                <?php echo strtoupper(substr($user['uye_adi'], 0, 1)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo clean($user['uye_adi']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: <?php echo $user['uye_id']; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-sort="email" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo clean($user['uye_eposta']); ?></div>
                            </td>
                            <td data-sort="status" class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    1 => 'bg-green-100 text-green-800',
                                    0 => 'bg-red-100 text-red-800'
                                ];
                                $statusTexts = [
                                    1 => 'Aktif',
                                    0 => 'Yasaklı'
                                ];
                                $status = $user['uye_onay'];
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$status]; ?>">
                                    <?php echo $statusTexts[$status]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $fa2StatusColors = [
                                    1 => 'bg-green-100 text-green-800',
                                    0 => 'bg-red-100 text-red-800'
                                ];
                                $fa2StatusTexts = [
                                    1 => 'Aktif',
                                    0 => 'Devre Dışı'
                                ];
                                $fa2Status = $user['uye_2fa_enabled'];
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $fa2StatusColors[$fa2Status]; ?>">
                                    <?php echo $fa2StatusTexts[$fa2Status]; ?>
                                </span>
                            </td>
                            <td data-sort="created_at" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($user['uye_tarih']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="editUser(<?php echo $user['uye_id']; ?>, '<?php echo addslashes($user['uye_adi']); ?>', '<?php echo addslashes($user['uye_eposta']); ?>')" 
                                            class="text-blue-600 hover:text-blue-900" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggle2FA(<?php echo $user['uye_id']; ?>, <?php echo $fa2Status; ?>)" 
                                            class="text-purple-600 hover:text-purple-900" title="2FA Durumu">
                                        <i class="fas fa-shield-alt"></i>
                                    </button>
                                    <button onclick="toggleUserStatus(<?php echo $user['uye_id']; ?>, <?php echo $status; ?>)" 
                                            class="text-yellow-600 hover:text-yellow-900" title="Durumu Değiştir">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <button onclick="deleteUser(<?php echo $user['uye_id']; ?>)" 
                                            class="text-red-600 hover:text-red-900" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Önceki
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Sonraki
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span class="font-medium"><?php echo (($page - 1) * $perPage) + 1; ?></span>
                                -
                                <span class="font-medium"><?php echo min($page * $perPage, $totalUsers); ?></span>
                                arası, toplam
                                <span class="font-medium"><?php echo number_format($totalUsers); ?></span>
                                kullanıcı
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Toggle user status
function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    const statusText = newStatus === 1 ? 'aktif' : 'yasaklı';
    showConfirm(`Bu kullanıcıyı ${statusText} yapmak istediğinizden emin misiniz?`, function() {
        const formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('user_id', userId);
        formData.append('current_status', currentStatus === 1 ? 'active' : 'banned');
        formData.append('ajax', '1');
        
        fetch('users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

// Delete user
function deleteUser(userId) {
    showConfirm('Bu kullanıcıyı devre dışı bırakmak istediğinizden emin misiniz?', function() {
        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('user_id', userId);
        formData.append('ajax', '1');
        
        fetch('users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

// Edit user
function editUser(userId, name, email) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    
    document.getElementById('editUserModal').classList.remove('hidden');
}

// Toggle 2FA
function toggle2FA(userId, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    const statusText = newStatus === 1 ? 'aktif' : 'devre dışı';
    
    showConfirm(`Bu kullanıcı için 2FA'yı ${statusText} hale getirmek istediğinizden emin misiniz?`, function() {
        const formData = new FormData();
        formData.append('action', 'toggle_2fa');
        formData.append('user_id', userId);
        formData.append('current_status', currentStatus);
        formData.append('ajax', '1');
        
        fetch('users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

// Export users
function exportUsers() {
    window.location.href = 'export/users.php';
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Handle filter form submission
    const filterForm = document.querySelector('form[method="GET"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Form will submit normally
        });
    }
});
</script>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kullanıcı Düzenle</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="edit_user_name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_user_name" name="name" required>
                                </div>
                                <div>
                                    <label for="edit_user_email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                    <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_user_email" name="email" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Güncelle
                    </button>
                    <button type="button" id="edit_user_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cancel edit user modal
document.getElementById('edit_user_cancel').addEventListener('click', function() {
    document.getElementById('editUserModal').classList.add('hidden');
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const editUserModal = document.getElementById('editUserModal');
    if (event.target === editUserModal) {
        editUserModal.classList.add('hidden');
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>