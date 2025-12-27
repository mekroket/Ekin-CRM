<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';

// Ödeme Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $project_id = $_POST['project_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO payments (project_id, amount, due_date, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$project_id, $amount, $due_date, $status]);
    header('Location: payments.php?success=1');
    exit;
}

// Ödeme Durumu Güncelleme
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['update_status'];
    $stmt = $pdo->prepare("UPDATE payments SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header('Location: payments.php?updated=1');
    exit;
}

$payments = $pdo->query("SELECT py.*, p.title as project_title, c.name as client_name FROM payments py JOIN projects p ON py.project_id = p.id JOIN clients c ON p.client_id = c.id ORDER BY py.created_at DESC")->fetchAll();
$projects = $pdo->query("SELECT id, title FROM projects ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">    <title>Ödemeler - EkinCRM</title>
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
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Ödemeler</h1>
                    <p class="text-slate-500 dark:text-slate-400">Ödeme kayıtlarını ve durumlarını buradan yönetin.</p>
                </div>
                <button onclick="document.getElementById('addPaymentModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Yeni Ödeme Kaydı
                </button>
            </header>

            <!-- Payment List -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Proje /
                                Müşteri</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Tutar</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Vade Tarihi
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Durum</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">
                                İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Henüz ödeme
                                    kaydı bulunmuyor.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-white">
                                            <?php echo htmlspecialchars($payment['project_title'] ?? ''); ?>
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($payment['client_name'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">
                                        ₺<?php echo number_format($payment['amount'] ?? 0, 2); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        <?php echo isset($payment['due_date']) ? date('d.m.Y', strtotime($payment['due_date'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full <?php echo ($payment['status'] ?? '') === 'Ödeme Alındı' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400'; ?>">
                                            <?php echo htmlspecialchars($payment['status'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <div class="relative inline-block text-left dropdown-container">
                                            <button onclick="toggleDropdown(this)"
                                                class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800">
                                                <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                            </button>
                                            <div
                                                class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl shadow-xl z-50 dropdown-menu transition-colors">
                                                <div class="py-1">
                                                    <a href="?update_status=Ödeme Alındı&id=<?php echo $payment['id']; ?>"
                                                        class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800">Ödeme
                                                        Alındı Olarak İşaretle</a>
                                                    <a href="?update_status=Bekliyor&id=<?php echo $payment['id']; ?>"
                                                        class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800">Bekliyor
                                                        Olarak İşaretle</a>
                                                    <a href="?update_status=Fatura Kesildi&id=<?php echo $payment['id']; ?>"
                                                        class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800">Fatura
                                                        Kesildi Olarak İşaretle</a>
                                                    <a href="exports/generate_pdf.php?id=<?php echo $payment['id']; ?>" target="_blank"
                                                        class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-slate-50 dark:hover:bg-zinc-800 font-medium">Teklif
                                                        Oluştur (PDF)</a>
                                                    <a href="exports/generate_invoice.php?id=<?php echo $payment['id']; ?>"
                                                        target="_blank"
                                                        class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-slate-50 dark:hover:bg-zinc-800 font-medium">Fatura
                                                        Oluştur</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Payment Modal -->
    <div id="addPaymentModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Yeni Ödeme Kaydı</h3>
                <button onclick="document.getElementById('addPaymentModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="payments.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_payment" value="1">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Proje Seçin</label>
                    <select name="project_id" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo $project['id']; ?>">
                                <?php echo htmlspecialchars($project['title'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tutar (₺)</label>
                    <input type="number" step="0.01" name="amount" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vade Tarihi</label>
                    <input type="date" name="due_date" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Durum</label>
                    <select name="status" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <option value="Bekliyor">Bekliyor</option>
                        <option value="Ödeme Alındı">Ödeme Alındı</option>
                        <option value="Fatura Kesildi">Fatura Kesildi</option>
                    </select>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('addPaymentModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;

            // Diğer tüm açık dropdownları kapat
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                }
            });

            // Mevcut dropdown'ı aç/kapat
            dropdown.classList.toggle('hidden');
        }

        // Dışarı tıklandığında kapatma
        window.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
</body>

</html>
