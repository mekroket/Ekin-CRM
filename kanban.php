<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';

$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

if ($project_id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();
}

$tasks = [];
if ($project_id) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC");
    $stmt->execute([$project_id]);
    $tasks = $stmt->fetchAll();
} else {
    $tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC")->fetchAll();
}

$projects = $pdo->query("SELECT id, title FROM projects ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <title>Kanban - EkinCRM</title>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .kanban-column {
            min-height: 200px;
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
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kanban Panosu</h1>
                    <p class="text-slate-500 dark:text-slate-400">
                        <?php echo $project_id ? htmlspecialchars($project['title'] ?? '') . " projesi görevleri" : "Tüm görevler"; ?>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <select onchange="window.location.href='kanban.php?project_id='+this.value"
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-zinc-800 focus:ring-2 focus:ring-indigo-500 outline-none bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white transition-colors">
                        <option value="">Tüm Projeler</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $project_id == $p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['title'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="document.getElementById('addTaskModal').classList.remove('hidden')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                        Yeni Görev
                    </button>
                </div>
            </header>

            <!-- Kanban Board -->
            <div class="flex space-x-6 overflow-x-auto pb-4">
                <?php
                $statuses = ['Bekliyor', 'Devam Ediyor', 'Bitti'];
                foreach ($statuses as $status):
                    ?>
                    <div class="flex-shrink-0 w-80">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-sm">
                                <?php echo $status; ?>
                            </h3>
                            <span
                                class="bg-slate-200 dark:bg-zinc-800 text-slate-600 dark:text-slate-400 px-2 py-0.5 rounded-full text-xs font-bold">
                                <?php
                                echo count(array_filter($tasks, function ($t) use ($status) {
                                    return $t['status'] === $status;
                                }));
                                ?>
                            </span>
                        </div>
                        <div id="column-<?php echo str_replace(' ', '-', $status); ?>" data-status="<?php echo $status; ?>"
                            class="kanban-column space-y-4 p-2 bg-slate-100/50 dark:bg-zinc-900/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-zinc-800 transition-colors">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task['status'] === $status): ?>
                                    <div data-id="<?php echo $task['id']; ?>"
                                        class="kanban-card bg-white dark:bg-zinc-900 p-4 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm cursor-move hover:shadow-md transition-all">
                                        <h4 class="font-semibold text-zinc-900 dark:text-white mb-2">
                                            <?php echo htmlspecialchars($task['title'] ?? ''); ?>
                                        </h4>
                                        <div class="flex items-center justify-between mt-4">
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase">
                                                <?php echo date('d M', strtotime($task['created_at'])); ?>
                                            </span>
                                            <div class="flex -space-x-2">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">
                                                    U</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Yeni Görev Ekle</h3>
                <button onclick="document.getElementById('addTaskModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="addTaskForm" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Görev
                        Başlığı</label>
                    <input type="text" name="title" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Proje</label>
                    <select name="project_id" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <?php foreach ($projects as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $project_id == $p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['title'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Durum</label>
                    <select name="status" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <option value="Bekliyor">Bekliyor</option>
                        <option value="Devam Ediyor">Devam Ediyor</option>
                        <option value="Bitti">Bitti</option>
                    </select>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // SortableJS initialization
        const columns = ['column-Bekliyor', 'column-Devam-Ediyor', 'column-Bitti'];
        columns.forEach(id => {
            const el = document.getElementById(id);
            new Sortable(el, {
                group: 'kanban',
                animation: 150,
                onEnd: function (evt) {
                    const taskId = evt.item.getAttribute('data-id');
                    const newStatus = evt.to.getAttribute('data-status');

                    // AJAX call to update status
                    fetch('update_task_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `id=${taskId}&status=${newStatus}`
                    });
                }
            });
        });

        // Add Task Form Submission
        document.getElementById('addTaskForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('processes/add_task_process.php', {
                method: 'POST',
                body: formData
            }).then(() => window.location.reload());
        });
    </script>
</body>

</html>