<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Kategori Yönetimi';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $parent_id = (int)$_POST['parent_id'];
        
        if (!empty($name)) {
            try {
                $stmt = $db->prepare("INSERT INTO kategoriler (kategori_adi, kategori_aciklama, kategori_ust_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $parent_id]);
                $success = "Kategori başarıyla eklendi.";
            } catch (Exception $e) {
                $error = "Kategori eklenirken bir hata oluştu: " . $e->getMessage();
            }
        } else {
            $error = "Kategori adı boş olamaz.";
        }
    } elseif (isset($_POST['edit_category'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $parent_id = (int)$_POST['parent_id'];
        
        // Prevent category from being its own parent
        if ($id === $parent_id) {
            $error = "Bir kategori kendisinin üst kategorisi olamaz.";
        } elseif (!empty($name)) {
            try {
                $stmt = $db->prepare("UPDATE kategoriler SET kategori_adi = ?, kategori_aciklama = ?, kategori_ust_id = ? WHERE kategori_id = ?");
                $stmt->execute([$name, $description, $parent_id, $id]);
                $success = "Kategori başarıyla güncellendi.";
            } catch (Exception $e) {
                $error = "Kategori güncellenirken bir hata oluştu: " . $e->getMessage();
            }
        } else {
            $error = "Kategori adı boş olamaz.";
        }
    } elseif (isset($_POST['delete_category'])) {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM kategoriler WHERE kategori_id = ?");
            $stmt->execute([$id]);
            $success = "Kategori başarıyla silindi.";
        } catch (Exception $e) {
            $error = "Kategori silinirken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch all categories for parent dropdown (only top-level categories for add form)
$stmt = $db->prepare("SELECT kategori_id, kategori_adi FROM kategoriler WHERE kategori_ust_id = 0 ORDER BY kategori_adi ASC");
$stmt->execute();
$parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for edit modal (all categories)
$stmt = $db->prepare("SELECT kategori_id, kategori_adi FROM kategoriler ORDER BY kategori_adi ASC");
$stmt->execute();
$allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories with product count and parent info
$stmt = $db->prepare("
    SELECT k.*, k2.kategori_adi as parent_kategori_adi, COUNT(u.urun_id) as product_count 
    FROM kategoriler k 
    LEFT JOIN kategoriler k2 ON k.kategori_ust_id = k2.kategori_id
    LEFT JOIN urunler u ON k.kategori_id = u.urun_kategori 
    GROUP BY k.kategori_id 
    ORDER BY k.kategori_adi ASC
");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total categories and products
$totalCategories = count($categories);
$totalProductsInCategories = 0;
foreach ($categories as $category) {
    $totalProductsInCategories += $category['product_count'];
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kategori Yönetimi</h1>
            <p class="text-gray-600 mt-1">Ürün kategorilerini yönetin</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-folder text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Kategori</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalCategories; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kategorilerdeki Ürünler</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalProductsInCategories; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Form -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Yeni Kategori Ekle</h2>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="add_category" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="name" name="name" required>
                        </div>
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="parent_id" name="parent_id">
                                <option value="0">Yok (Ana Kategori)</option>
                                <?php if (!empty($parentCategories)): ?>
                                    <?php foreach ($parentCategories as $category): ?>
                                        <option value="<?php echo $category['kategori_id']; ?>">
                                            <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-plus mr-2"></i>
                            Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Kategoriler</h2>
                <div class="relative">
                    <input type="text" class="w-full sm:w-64 px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ara...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-folder text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz kategori eklenmemiş</h3>
                        <p class="text-gray-500">Yukarıdaki formu kullanarak yeni kategori ekleyebilirsiniz</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üst Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Sayısı</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($categories as $category): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $category['kategori_id']; ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($category['kategori_adi']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($category['parent_kategori_adi'] ?? 'Ana Kategori'); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($category['kategori_aciklama'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo $category['product_count']; ?> ürün
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button class="edit-category inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        data-id="<?php echo $category['kategori_id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($category['kategori_adi']); ?>" 
                                                        data-description="<?php echo htmlspecialchars($category['kategori_aciklama'] ?? ''); ?>"
                                                        data-parent="<?php echo $category['kategori_ust_id']; ?>">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Düzenle
                                                </button>
                                                <?php if ($category['product_count'] == 0): ?>
                                                    <button class="delete-category inline-flex items-center px-3 py-1.5 border border-transparent text-sm rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                            data-id="<?php echo $category['kategori_id']; ?>">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Sil
                                                    </button>
                                                <?php else: ?>
                                                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" disabled>
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Sil
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="edit_category" value="1">
                <input type="hidden" id="edit_id" name="id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kategori Düzenle</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_name" name="name" required>
                                </div>
                                <div>
                                    <label for="edit_parent_id" class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_parent_id" name="parent_id">
                                        <option value="0">Yok (Ana Kategori)</option>
                                        <?php if (!empty($allCategories)): ?>
                                            <?php foreach ($allCategories as $category): ?>
                                                <option value="<?php echo $category['kategori_id']; ?>">
                                                    <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Güncelle
                    </button>
                    <button type="button" id="edit_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div id="deleteCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="delete_category" value="1">
                <input type="hidden" id="delete_id" name="id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kategori Sil</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Bu kategoriyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Sil
                    </button>
                    <button type="button" id="delete_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit category button click
    const editButtons = document.querySelectorAll('.edit-category');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const parentId = this.dataset.parent || 0;
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_parent_id').value = parentId;
            
            // Disable the current category in the parent selection dropdown to prevent self-reference
            const parentSelect = document.getElementById('edit_parent_id');
            if (parentSelect) {
                const options = parentSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value == id) {
                        option.disabled = true;
                        option.style.display = 'none';
                    } else {
                        option.disabled = false;
                        option.style.display = '';
                    }
                });
            }
            
            document.getElementById('editCategoryModal').classList.remove('hidden');
        });
    });
    
    // Delete category button click
    const deleteButtons = document.querySelectorAll('.delete-category');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            
            document.getElementById('delete_id').value = id;
            
            document.getElementById('deleteCategoryModal').classList.remove('hidden');
        });
    });
    
    // Cancel buttons
    document.getElementById('edit_cancel').addEventListener('click', function() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    });
    
    document.getElementById('delete_cancel').addEventListener('click', function() {
        document.getElementById('deleteCategoryModal').classList.add('hidden');
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editCategoryModal');
        const deleteModal = document.getElementById('deleteCategoryModal');
        
        if (event.target === editModal) {
            editModal.classList.add('hidden');
        }
        
        if (event.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>