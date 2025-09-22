<?php
require_once 'config.php';

// Get the requested page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define allowed pages
$allowedPages = [
    'home', 'products', 'product', 'cart', 'checkout', 'account', 'login', 'logout',
    'kayit', '2fa', 'login_2fa', 'download', 'category', 'iade', 'gizlilik', 'kullanim', 'cerezler', 'hakkimizda', 'sss', 'iletisim', 'kategoriler'
];

// Check if the requested page is allowed
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Special handling for account section - redirect if not logged in
if ($page === 'account' && !isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: index.php?page=login');
    exit;
}

// Special handling for admin section
if ($page === 'admin') {
    header('Location: admin/');
    exit;
}

// Special handling for login - redirect if already logged in
if ($page === 'login' && isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Special handling for registration - redirect if already logged in
if ($page === 'kayit' && isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Buffer output to prevent "headers already sent" issues
ob_start();
?>

<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0f172a',
                        light: '#f8fafc'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-color: #f1f5f9;
            --text-color: #0f172a;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg-color: #0f172a;
            --text-color: #f1f5f9;
            --card-bg: #1e293b;
            --border-color: #334155;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <?php include 'includes/header.php'; ?>
    
    <main class="flex-grow">
        <?php
        // Include the requested page
        $pagePath = 'pages/' . $page . '.php';
        if (file_exists($pagePath)) {
            include $pagePath;
        } else {
            // Default to home page if file doesn't exist
            include 'pages/home.php';
        }
        ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
// Flush the output buffer
ob_end_flush();
?>