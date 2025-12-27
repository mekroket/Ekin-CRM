<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';

// İstatistikleri çekelim
$client_count = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$project_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$active_projects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status != 'Tamamlandı'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Ödeme Alındı'")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EkinCRM</title>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-zinc-950 transition-colors duration-300">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Hoş geldin,
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'Kullanıcı'); ?>!
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400">İşte bugün neler oluyor.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                    </button>
                    <div
                        class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                        <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Toplam Müşteri</h3>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white"><?php echo $client_count; ?></p>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg">
                            <i data-lucide="briefcase" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Aktif Projeler</h3>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white"><?php echo $active_projects; ?></p>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Toplam Gelir</h3>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        ₺<?php echo number_format($total_revenue, 2); ?></p>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-lg">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Bekleyen İşler</h3>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">0</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Projects -->
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Son Projeler</h2>
                        <a href="projects.php"
                            class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Tümünü
                            Gör</a>
                    </div>
                    <div class="space-y-4">
                        <?php
                        $recent_projects = $pdo->query("SELECT p.*, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
                        if (empty($recent_projects)): ?>
                            <p class="text-slate-500 dark:text-slate-400 text-center py-4">Henüz proje bulunmuyor.</p>
                        <?php else: ?>
                            <?php foreach ($recent_projects as $project): ?>
                                <div
                                    class="flex items-center justify-between p-4 bg-slate-50 dark:bg-zinc-800/50 rounded-xl transition-colors">
                                    <div>
                                        <h4 class="font-semibold text-zinc-900 dark:text-white">
                                            <?php echo htmlspecialchars($project['title'] ?? ''); ?>
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($project['client_name'] ?? ''); ?>
                                        </p>
                                    </div>
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                        <?php echo htmlspecialchars($project['status'] ?? ''); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Son Ödemeler</h2>
                        <a href="payments.php"
                            class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Tümünü
                            Gör</a>
                    </div>
                    <div class="space-y-4">
                        <?php
                        $recent_payments = $pdo->query("SELECT py.*, p.title as project_title FROM payments py JOIN projects p ON py.project_id = p.id ORDER BY py.created_at DESC LIMIT 5")->fetchAll();
                        if (empty($recent_payments)): ?>
                            <p class="text-slate-500 dark:text-slate-400 text-center py-4">Henüz ödeme kaydı bulunmuyor.</p>
                        <?php else: ?>
                            <?php foreach ($recent_payments as $payment): ?>
                                <div
                                    class="flex items-center justify-between p-4 bg-slate-50 dark:bg-zinc-800/50 rounded-xl transition-colors">
                                    <div>
                                        <h4 class="font-semibold text-zinc-900 dark:text-white">
                                            <?php echo htmlspecialchars($payment['project_title'] ?? ''); ?>
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            <?php echo isset($payment['created_at']) ? date('d.m.Y', strtotime($payment['created_at'])) : '-'; ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-zinc-900 dark:text-white">
                                            ₺<?php echo number_format($payment['amount'] ?? 0, 2); ?>
                                        </p>
                                        <span
                                            class="text-xs font-medium <?php echo ($payment['status'] ?? '') === 'Ödeme Alındı' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'; ?>">
                                            <?php echo htmlspecialchars($payment['status'] ?? ''); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
