<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Destek Talepleri';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_status'])) {
        $ticket_id = (int)$_POST['ticket_id'];
        $status = $_POST['status'];
        $response = trim($_POST['response']);
        
        try {
            // Update ticket status
            $stmt = $db->prepare("UPDATE destek_biletleri SET bilet_durum = ?, bilet_yanit = ? WHERE bilet_id = ?");
            $stmt->execute([$status, $response, $ticket_id]);
            
            // Add to response history
            if (!empty($response)) {
                $stmt = $db->prepare("INSERT INTO destek_yanitlari (bilet_id, uye_id, yanit_icerik, yanit_tarih) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$ticket_id, $_SESSION['user_id'], $response]);
            }
            
            $success = "Destek talebi durumu başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Destek talebi güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch tickets with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total tickets count and status breakdown
$stmt = $db->prepare("SELECT COUNT(*) FROM destek_biletleri");
$stmt->execute();
$totalTickets = $stmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalTickets / $limit);

// Get status counts
$stmt = $db->prepare("SELECT bilet_durum, COUNT(*) as count FROM destek_biletleri GROUP BY bilet_durum");
$stmt->execute();
$statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get tickets with user info
$stmt = $db->prepare("
    SELECT d.*, u.uye_adi, u.uye_eposta 
    FROM destek_biletleri d 
    INNER JOIN uyeler u ON d.uye_id = u.uye_id 
    ORDER BY d.bilet_tarih DESC 
    LIMIT $offset, $limit
");
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Destek Talepleri</h1>
            <p class="mt-1 text-sm text-gray-500">Kullanıcı destek taleplerini yönetin</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Toplam Talep</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalTickets; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Açık Talepler</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['acik'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bekleyen</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['beklemede'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Kapalı Talepler</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['kapali'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Tüm Destek Talepleri</h2>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="Ara..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($tickets)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-ticket-alt text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz destek talebi bulunmuyor</h3>
                        <p class="text-gray-500">Kullanıcılar destek talebi oluşturduğunda burada listelenecek</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?php echo $ticket['bilet_id']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($ticket['uye_adi']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo htmlspecialchars(substr($ticket['bilet_baslik'], 0, 30)) . (strlen($ticket['bilet_baslik']) > 30 ? '...' : ''); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $statusClasses = [
                                                'acik' => 'bg-yellow-100 text-yellow-800',
                                                'beklemede' => 'bg-blue-100 text-blue-800',
                                                'kapali' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusTexts = [
                                                'acik' => 'Açık',
                                                'beklemede' => 'Beklemede',
                                                'kapali' => 'Kapalı'
                                            ];
                                            ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?php echo $statusClasses[$ticket['bilet_durum']]; ?>">
                                                <?php echo $statusTexts[$ticket['bilet_durum']]; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d.m.Y H:i', strtotime($ticket['bilet_tarih'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button 
                                                    class="view-ticket text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg"
                                                    data-id="<?php echo $ticket['bilet_id']; ?>">
                                                    <i class="fas fa-eye mr-1"></i> Detay
                                                </button>
                                                <div class="relative inline-block text-left">
                                                    <button 
                                                        type="button" 
                                                        class="status-dropdown bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg flex items-center"
                                                        data-id="<?php echo $ticket['bilet_id']; ?>">
                                                        <i class="fas fa-cog mr-1"></i> Durum
                                                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                                    </button>
                                                    <div class="status-dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10 hidden">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="update_status" value="1">
                                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['bilet_id']; ?>">
                                                            <input type="hidden" name="status" value="acik">
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Açık</button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="update_status" value="1">
                                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['bilet_id']; ?>">
                                                            <input type="hidden" name="status" value="beklemede">
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beklemede</button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="update_status" value="1">
                                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['bilet_id']; ?>">
                                                            <input type="hidden" name="status" value="kapali">
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kapalı</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Toplam <?php echo $totalTickets; ?> talep, <?php echo $limit; ?> tanesi gösteriliyor
                            </div>
                            <div class="flex space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Önceki
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" 
                                       class="px-3 py-2 rounded-md text-sm font-medium <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Sonraki
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Ticket Modal -->
<div id="viewTicketModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Destek Talebi Detayları</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6" id="ticket-details">
            <!-- Ticket details will be loaded here via AJAX -->
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-500">Yükleniyor...</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                Kapat
            </button>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('viewTicketModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // View ticket button click
    const viewButtons = document.querySelectorAll('.view-ticket');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const ticketId = this.dataset.id;
            
            // Show modal
            const modal = document.getElementById('viewTicketModal');
            modal.classList.remove('hidden');
            
            // Load ticket details via AJAX
            fetch('ajax/get_ticket_details.php?id=' + ticketId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('ticket-details').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('ticket-details').innerHTML = '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Talep detayları yüklenirken hata oluştu.</div>';
                });
        });
    });
    
    // Status dropdown functionality
    const dropdownButtons = document.querySelectorAll('.status-dropdown');
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = this.nextElementSibling;
            const isVisible = !dropdownMenu.classList.contains('hidden');
            
            // Close all dropdowns
            document.querySelectorAll('.status-dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
            
            // Toggle current dropdown
            if (!isVisible) {
                dropdownMenu.classList.remove('hidden');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.status-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });
    
    // Prevent closing dropdown when clicking inside
    document.querySelectorAll('.status-dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>