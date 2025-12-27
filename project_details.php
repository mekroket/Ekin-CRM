<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: projects.php');
    exit;
}
require_once 'includes/db.php';

$project_id = $_GET['id'];

// Proje ve Müşteri bilgilerini çek
$stmt = $pdo->prepare("SELECT p.*, c.name as client_name, c.email as client_email, c.phone as client_phone, c.company as client_company 
                       FROM projects p 
                       JOIN clients c ON p.client_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: projects.php');
    exit;
}

// Görevleri çek
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC");
$stmt->execute([$project_id]);
$tasks = $stmt->fetchAll();

// Ödemeleri çek
$stmt = $pdo->prepare("SELECT * FROM payments WHERE project_id = ? ORDER BY created_at DESC");
$stmt->execute([$project_id]);
$payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title'] ?? ''); ?> - EkinCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
            <div class="p-6">
                <h2 class="text-xl font-bold text-indigo-600">EkinCRM</h2>
            </div>
            <nav class="flex-1 px-4 space-y-1">
                <a href="index.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                    Dashboard
                </a>
                <a href="clients.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                    Müşteriler
                </a>
                <a href="projects.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                    Projeler
                </a>
                <a href="kanban.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="layout" class="w-5 h-5 mr-3"></i>
                    Kanban
                </a>
                <a href="payments.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-3"></i>
                    Ödemeler
                </a>
                <a href="accounting.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i>
                    Muhasebe
                </a>
                <a href="settings.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                    Ayarlar
                </a>
            </nav>
            <div class="p-4 border-t border-slate-100">
                <a href="logout.php"
                    class="flex items-center px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                    Çıkış Yap
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <a href="projects.php"
                        class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900">
                            <?php echo htmlspecialchars($project['title'] ?? ''); ?>
                        </h1>
                        <p class="text-slate-500"><?php echo htmlspecialchars($project['client_name'] ?? ''); ?> /
                            <?php echo htmlspecialchars($project['client_company'] ?? ''); ?>
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="kanban.php?project_id=<?php echo $project['id']; ?>"
                        class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl flex items-center hover:bg-slate-50 transition-all">
                        <i data-lucide="layout" class="w-5 h-5 mr-2"></i>
                        Kanban'da Gör
                    </a>
                    <button
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                        <i data-lucide="edit-2" class="w-5 h-5 mr-2"></i>
                        Düzenle
                    </button>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Description -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-lg font-bold text-zinc-900 mb-4">Proje Açıklaması</h3>
                        <p class="text-slate-600 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($project['description'] ?? 'Açıklama belirtilmemiş.')); ?>
                        </p>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-zinc-900">Görevler</h3>
                            <span class="text-sm text-slate-500"><?php echo count($tasks); ?> Görev</span>
                        </div>
                        <div class="space-y-3">
                            <?php if (empty($tasks)): ?>
                                <p class="text-slate-500 text-center py-4 text-sm">Henüz görev eklenmemiş.</p>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <div
                                        class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-transparent hover:border-slate-200 transition-all">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-4 <?php
                                            echo $task['status'] === 'Bitti' ? 'bg-green-500' :
                                                ($task['status'] === 'Devam Ediyor' ? 'bg-blue-500' : 'bg-orange-500');
                                            ?>"></div>
                                            <span
                                                class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($task['title'] ?? ''); ?></span>
                                        </div>
                                        <span
                                            class="text-xs font-medium text-slate-400"><?php echo htmlspecialchars($task['status'] ?? ''); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Info & Payments -->
                <div class="space-y-8">
                    <!-- Project Info Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-lg font-bold text-zinc-900 mb-6">Proje Özeti</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Durum</span>
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-600">
                                    <?php echo htmlspecialchars($project['status'] ?? ''); ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Bütçe</span>
                                <span
                                    class="text-sm font-bold text-zinc-900">₺<?php echo number_format($project['budget'] ?? 0, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Teslim Tarihi</span>
                                <span
                                    class="text-sm font-medium text-zinc-900"><?php echo isset($project['deadline']) ? date('d.m.Y', strtotime($project['deadline'])) : '-'; ?></span>
                            </div>
                            <div class="pt-4 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-400 uppercase block mb-3">Müşteri
                                    İletişim</span>
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm text-slate-600">
                                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                        <?php echo htmlspecialchars($project['client_email'] ?? ''); ?>
                                    </div>
                                    <div class="flex items-center text-sm text-slate-600">
                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                        <?php echo htmlspecialchars($project['client_phone'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-zinc-900">Ödemeler</h3>
                            <a href="payments.php" class="text-xs text-indigo-600 hover:underline">Yönet</a>
                        </div>
                        <div class="space-y-4">
                            <?php if (empty($payments)): ?>
                                <p class="text-slate-500 text-center py-4 text-sm">Ödeme kaydı yok.</p>
                            <?php else: ?>
                                <?php foreach ($payments as $payment): ?>
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <p class="text-sm font-bold text-zinc-900">
                                                ₺<?php echo number_format($payment['amount'] ?? 0, 2); ?></p>
                                            <p class="text-xs text-slate-500">
                                                <?php echo isset($payment['due_date']) ? date('d.m.Y', strtotime($payment['due_date'])) : '-'; ?>
                                            </p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-md <?php
                                        echo ($payment['status'] ?? '') === 'Ödeme Alındı' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600';
                                        ?>">
                                            <?php echo htmlspecialchars($payment['status'] ?? ''); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
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
