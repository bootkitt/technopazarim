<?php
// Check if creating a new ticket or viewing existing ones
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$ticketId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action === 'create') {
    // Check if user already has an open ticket
    $stmt = $db->prepare("SELECT COUNT(*) FROM destek_biletleri WHERE uye_id = ? AND bilet_durum = 'acik'");
    $stmt->execute([$_SESSION['user_id']]);
    $openTickets = $stmt->fetchColumn();
    
    if ($openTickets > 0) {
        echo '<div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded-lg">Zaten açık bir destek talebiniz bulunmaktadır. Yeni talep oluşturmadan önce mevcut talebinizin kapanmasını bekleyin.</div>';
        $action = 'list';
    } else {
        // Handle form submission
        if ($_POST) {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            $priority = isset($_POST['priority']) ? $_POST['priority'] : 'orta';
            
            // Validate input
            if (empty($title) || empty($content)) {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Lütfen tüm alanları doldurun.</div>';
            } elseif (strlen($title) < 5) {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Başlık en az 5 karakter olmalıdır.</div>';
            } elseif (strlen($content) < 10) {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Mesajınız en az 10 karakter olmalıdır.</div>';
            } else {
                // Create ticket
                $stmt = $db->prepare("INSERT INTO destek_biletleri (uye_id, bilet_baslik, bilet_icerik, bilet_oncelik) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$_SESSION['user_id'], $title, $content, $priority]);
                
                if ($result) {
                    echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">Destek talebiniz başarıyla oluşturuldu. En kısa sürede yanıtlanacaktır.</div>';
                    $action = 'list';
                } else {
                    echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Destek talebi oluşturulurken bir hata oluştu.</div>';
                }
            }
        }
        
        if ($action === 'create') {
            ?>
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Yeni Destek Talebi</h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-300">Yeni bir destek talebi oluşturun</p>
                </div>
                <a href="index.php?page=account&section=support" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Geri
                </a>
            </div>
            
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Talep Bilgileri</h2>
                </div>
                <div class="p-6">
                    <form method="POST">
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Başlık</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                        </div>
                        <div class="mb-6">
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Öncelik</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="priority" name="priority">
                                <option value="dusuk" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'dusuk') ? 'selected' : ''; ?>>Düşük</option>
                                <option value="orta" <?php echo (!isset($_POST['priority']) || $_POST['priority'] === 'orta') ? 'selected' : ''; ?>>Orta</option>
                                <option value="yuksek" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'yuksek') ? 'selected' : ''; ?>>Yüksek</option>
                                <option value="acil" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'acil') ? 'selected' : ''; ?>>Acil</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mesajınız</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="content" name="content" rows="6" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i> Gönder
                        </button>
                    </form>
                </div>
            </div>
            <?php
        }
    }
} elseif ($action === 'view' && $ticketId > 0) {
    // Fetch ticket details
    $stmt = $db->prepare("SELECT d.*, u.uye_adi FROM destek_biletleri d INNER JOIN uyeler u ON d.uye_id = u.uye_id WHERE d.bilet_id = ? AND d.uye_id = ?");
    $stmt->execute([$ticketId, $_SESSION['user_id']]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        header('Location: index.php?page=account&section=support');
        exit;
    }
    
    // Fetch ticket messages
    $stmt = $db->prepare("SELECT * FROM bilet_mesajlari WHERE bilet_id = ? ORDER BY mesaj_tarih ASC");
    $stmt->execute([$ticketId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle reply submission
    if ($_POST && $ticket['bilet_durum'] === 'acik') {
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        if (!empty($message) && strlen($message) >= 3) {
            // Add reply
            $stmt = $db->prepare("INSERT INTO bilet_mesajlari (bilet_id, gonderen_id, mesaj_icerik, gonderen_tip) VALUES (?, ?, ?, 'uye')");
            $result = $stmt->execute([$ticketId, $_SESSION['user_id'], $message]);
            
            if ($result) {
                echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">Yanıtınız gönderildi.</div>';
                // Refresh messages
                $stmt = $db->prepare("SELECT * FROM bilet_mesajlari WHERE bilet_id = ? ORDER BY mesaj_tarih ASC");
                $stmt->execute([$ticketId]);
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Yanıt gönderilirken bir hata oluştu.</div>';
            }
        } else {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Mesaj en az 3 karakter olmalıdır.</div>';
        }
    }
    
    // Handle ticket closing
    if (isset($_POST['close_ticket']) && $ticket['bilet_durum'] === 'acik') {
        $stmt = $db->prepare("UPDATE destek_biletleri SET bilet_durum = 'kapali' WHERE bilet_id = ? AND uye_id = ?");
        $result = $stmt->execute([$ticketId, $_SESSION['user_id']]);
        
        if ($result) {
            echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">Destek talebi kapatıldı.</div>';
            // Refresh ticket
            $stmt = $db->prepare("SELECT d.*, u.uye_adi FROM destek_biletleri d INNER JOIN uyeler u ON d.uye_id = u.uye_id WHERE d.bilet_id = ? AND d.uye_id = ?");
            $stmt->execute([$ticketId, $_SESSION['user_id']]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Destek talebi kapatılırken bir hata oluştu.</div>';
        }
    }
    ?>
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Destek Talebi #<?php echo $ticket['bilet_id']; ?></h1>
            <p class="mt-1 text-gray-600 dark:text-gray-300">Talep detaylarını ve mesaj geçmişini görüntüleyin</p>
        </div>
        <a href="index.php?page=account&section=support" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Geri
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="card rounded-2xl overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Mesaj Geçmişi</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($messages)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400">Henüz mesaj yok.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($messages as $message): ?>
                                <div class="card rounded-lg overflow-hidden <?php echo $message['gonderen_tip'] === 'uye' ? 'bg-gray-100 dark:bg-gray-800' : 'border border-indigo-200 dark:border-indigo-800'; ?>">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h6 class="font-medium text-gray-900 dark:text-white">
                                                <?php echo $message['gonderen_tip'] === 'uye' ? 'Siz' : 'Destek Ekibi'; ?>
                                            </h6>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('d.m.Y H:i', strtotime($message['mesaj_tarih'])); ?></span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?php echo htmlspecialchars($message['mesaj_icerik']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($ticket['bilet_durum'] === 'acik'): ?>
                <div class="card rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Yanıtla</h2>
                    </div>
                    <div class="p-6">
                        <form method="POST">
                            <div class="mb-4">
                                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" name="message" rows="4" placeholder="Mesajınızı yazın..." required></textarea>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i> Gönder
                                </button>
                                <button type="submit" name="close_ticket" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" onclick="return confirm('Destek talebini kapatmak istediğinize emin misiniz?')">
                                    <i class="fas fa-times-circle mr-2"></i> Talebi Kapat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-4 bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i> Bu destek talebi kapalıdır. Yeni yanıt gönderilemez.
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Talep Detayları</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Başlık</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($ticket['bilet_baslik']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Oluşturan</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($ticket['uye_adi']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Durum</dt>
                            <dd class="mt-1">
                                <?php
                                switch ($ticket['bilet_durum']) {
                                    case 'acik':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Açık</span>';
                                        break;
                                    case 'beklemede':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Beklemede</span>';
                                        break;
                                    case 'kapali':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Kapalı</span>';
                                        break;
                                    default:
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                }
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Öncelik</dt>
                            <dd class="mt-1">
                                <?php
                                switch ($ticket['bilet_oncelik']) {
                                    case 'dusuk':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Düşük</span>';
                                        break;
                                    case 'orta':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">Orta</span>';
                                        break;
                                    case 'yuksek':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Yüksek</span>';
                                        break;
                                    case 'acil':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Acil</span>';
                                        break;
                                    default:
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                }
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarih</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo date('d.m.Y H:i', strtotime($ticket['bilet_tarih'])); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // List all tickets
    $stmt = $db->prepare("SELECT * FROM destek_biletleri WHERE uye_id = ? ORDER BY bilet_tarih DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Talep Geçmişi</h2>
                <a href="index.php?page=account&section=support&action=create" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus-circle mr-2"></i> Yeni Talep
                </a>
        </div>
        <div class="p-6">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Henüz destek talebiniz bulunmuyor</h3>
                    <p class="text-gray-500 dark:text-gray-400">İlk destek talebinizi oluşturmak için aşağıdaki butona tıklayın.</p>
                    <a href="index.php?page=account&section=support&action=create" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i> İlk Talebi Oluştur
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Talep ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Başlık</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Öncelik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#<?php echo $ticket['bilet_id']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars(substr($ticket['bilet_baslik'], 0, 50)) . (strlen($ticket['bilet_baslik']) > 50 ? '...' : ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        switch ($ticket['bilet_durum']) {
                                            case 'acik':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Açık</span>';
                                                break;
                                            case 'beklemede':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Beklemede</span>';
                                                break;
                                            case 'kapali':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Kapalı</span>';
                                                break;
                                            default:
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        switch ($ticket['bilet_oncelik']) {
                                            case 'dusuk':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Düşük</span>';
                                                break;
                                            case 'orta':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">Orta</span>';
                                                break;
                                            case 'yuksek':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Yüksek</span>';
                                                break;
                                            case 'acil':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Acil</span>';
                                                break;
                                            default:
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo date('d.m.Y', strtotime($ticket['bilet_tarih'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="index.php?page=account&section=support&action=view&id=<?php echo $ticket['bilet_id']; ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            <i class="fas fa-eye mr-1"></i> Görüntüle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}
?>