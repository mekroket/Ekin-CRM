<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: projects.php');
    exit;
}
require_once 'includes/db.php';

$project_id = $_GET['id'];

// Proje Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $title = $_POST['title'];
    $client_id = $_POST['client_id'];
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE projects SET title = ?, client_id = ?, budget = ?, deadline = ?, description = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $client_id, $budget, $deadline, $description, $status, $project_id]);

    header("Location: project_details.php?id=$project_id&success=1");
    exit;
}

// Mevcut Proje Bilgilerini Çek
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: projects.php');
    exit;
}

$clients = $pdo->query("SELECT id, name FROM clients ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <title>Projeyi Düzenle - EkinCRM</title>
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
                <div class="flex items-center space-x-4">
                    <a href="project_details.php?id=<?php echo $project_id; ?>"
                        class="p-2 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Projeyi Düzenle</h1>
                        <p class="text-slate-500 dark:text-slate-400">Proje detaylarını güncelleyin.</p>
                    </div>
                </div>
            </header>

            <div class="max-w-2xl mx-auto">
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm p-8">
                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="update_project" value="1">

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Proje
                                Başlığı</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Müşteri</label>
                            <select name="client_id" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['id']; ?>" <?php echo $client['id'] == $project['client_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bütçe
                                    (₺)</label>
                                <input type="number" name="budget"
                                    value="<?php echo htmlspecialchars($project['budget']); ?>" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Teslim
                                    Tarihi</label>
                                <input type="date" name="deadline"
                                    value="<?php echo htmlspecialchars($project['deadline']); ?>" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Durum</label>
                            <select name="status" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                                <option value="Devam Ediyor" <?php echo $project['status'] == 'Devam Ediyor' ? 'selected' : ''; ?>>Devam Ediyor</option>
                                <option value="Tamamlandı" <?php echo $project['status'] == 'Tamamlandı' ? 'selected' : ''; ?>>Tamamlandı</option>
                                <option value="İptal Edildi" <?php echo $project['status'] == 'İptal Edildi' ? 'selected' : ''; ?>>İptal Edildi</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Açıklama</label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"><?php echo htmlspecialchars($project['description']); ?></textarea>
                        </div>

                        <div class="pt-4 flex space-x-4">
                            <a href="project_details.php?id=<?php echo $project_id; ?>"
                                class="flex-1 px-4 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors text-center font-medium">İptal</a>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors font-medium shadow-lg shadow-indigo-500/20">Değişiklikleri
                                Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>