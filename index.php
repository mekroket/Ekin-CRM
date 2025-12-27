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

// Grafik Verileri - Aylık Gelir (Son 6 Ay)
$monthly_revenue_query = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total 
    FROM payments 
    WHERE status = 'Ödeme Alındı' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
    GROUP BY month 
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Son 6 ayın etiketlerini ve verilerini hazırla (Boş aylar için 0 doldur)
$revenue_labels = [];
$revenue_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $revenue_labels[] = date('F Y', strtotime("-$i months")); // Türkçe ay isimleri için setlocale gerekebilir, şimdilik İngilizce/Varsayılan
    $revenue_data[] = $monthly_revenue_query[$month] ?? 0;
}

// Grafik Verileri - Proje Durumları
$project_status_data = $pdo->query("SELECT status, COUNT(*) as count FROM projects GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$status_labels = array_keys($project_status_data);
$status_counts = array_values($project_status_data);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Revenue Chart -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Aylık Gelir Analizi</h2>
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
                <!-- Project Status Chart -->
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Proje Durumları</h2>
                    <div class="relative h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
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

        // Chart.js Konfigürasyonu
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const gridColor = isDarkMode ? '#334155' : '#e2e8f0';

        // Gelir Grafiği
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($revenue_labels); ?>,
                datasets: [{
                    label: 'Gelir (₺)',
                    data: <?php echo json_encode($revenue_data); ?>,
                    borderColor: '#4f46e5', // Indigo-600
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#18181b' : '#ffffff',
                        titleColor: isDarkMode ? '#ffffff' : '#0f172a',
                        bodyColor: isDarkMode ? '#cbd5e1' : '#334155',
                        borderColor: isDarkMode ? '#27272a' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return '₺' + context.parsed.y.toLocaleString('tr-TR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, borderDash: [5, 5] },
                        ticks: { color: textColor, callback: function (value) { return '₺' + value; } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });

        // Proje Durum Grafiği
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: [
                        '#4f46e5', // Planlama (Indigo)
                        '#0ea5e9', // Devam Ediyor (Sky)
                        '#f59e0b', // Test (Amber)
                        '#10b981', // Tamamlandı (Emerald)
                        '#ef4444'  // Diğer (Red)
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, usePointStyle: true, padding: 20 }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>

</html>