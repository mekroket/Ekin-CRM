<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Proje Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $title = $_POST['title'];
    $client_id = $_POST['client_id'];
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO projects (title, client_id, budget, deadline, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $client_id, $budget, $deadline, $description]);
    header('Location: projects.php?success=1');
    exit;
}

$projects = $pdo->query("SELECT p.*, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id ORDER BY p.created_at DESC")->fetchAll();
$clients = $pdo->query("SELECT id, name FROM clients ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeler - EkinCRM</title>
    <script src="theme.js"></script>
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
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Projeler</h1>
                    <p class="text-slate-500 dark:text-slate-400">Tüm projelerinizi ve durumlarını buradan izleyin.</p>
                </div>
                <button onclick="document.getElementById('addProjectModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Yeni Proje Başlat
                </button>
            </header>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($projects)): ?>
                    <div
                        class="col-span-full bg-white dark:bg-zinc-900 p-12 text-center rounded-2xl border border-slate-200 dark:border-zinc-800 transition-colors">
                        <p class="text-slate-500 dark:text-slate-400">Henüz proje bulunmuyor.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div
                            class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                        <?php echo htmlspecialchars($project['status'] ?? ''); ?>
                                    </span>
                                    <a href="project_details.php?id=<?php echo $project['id']; ?>"
                                        class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <i data-lucide="external-link" class="w-5 h-5"></i>
                                    </a>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-1">
                                    <?php echo htmlspecialchars($project['title'] ?? ''); ?>
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                    <?php echo htmlspecialchars($project['client_name'] ?? ''); ?>
                                </p>
                                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-zinc-800">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500 dark:text-slate-400">Bütçe</span>
                                        <span
                                            class="font-bold text-zinc-900 dark:text-white">₺<?php echo number_format($project['budget'] ?? 0, 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500 dark:text-slate-400">Teslim</span>
                                        <span
                                            class="font-medium text-zinc-900 dark:text-slate-300"><?php echo isset($project['deadline']) ? date('d.m.Y', strtotime($project['deadline'])) : '-'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-6 py-4 bg-slate-50 dark:bg-zinc-800/50 border-t border-slate-100 dark:border-zinc-800 rounded-b-2xl transition-colors">
                                <a href="kanban.php?project_id=<?php echo $project['id']; ?>"
                                    class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center">
                                    <i data-lucide="layout" class="w-4 h-4 mr-2"></i>
                                    Kanban Tahtası
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Project Modal -->
    <div id="addProjectModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Yeni Proje Başlat</h3>
                <button onclick="document.getElementById('addProjectModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="projects.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_project" value="1">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Proje
                        Başlığı</label>
                    <input type="text" name="title" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Müşteri
                        Seçin</label>
                    <select name="client_id" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>">
                                <?php echo htmlspecialchars($client['name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bütçe
                            (₺)</label>
                        <input type="number" name="budget" required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teslim
                            Tarihi</label>
                        <input type="date" name="deadline" required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Açıklama</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('addProjectModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Projeyi
                        Başlat</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
