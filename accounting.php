<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';

// Gider Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO expenses (category, amount, expense_date, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$category, $amount, $expense_date, $description]);
    header('Location: accounting.php?success=1');
    exit;
}

// İstatistikler
$total_income = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Ödeme Alındı'")->fetchColumn() ?: 0;
$total_expenses = $pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?: 0;
$net_profit = $total_income - $total_expenses;

$expenses = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muhasebe - EkinCRM</title>
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
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Muhasebe ve Finans</h1>
                    <p class="text-slate-500 dark:text-slate-400">Gelir, gider ve karlılık durumunuzu takip edin.</p>
                </div>
                <button onclick="document.getElementById('addExpenseModal').classList.remove('hidden')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                    <i data-lucide="minus-circle" class="w-5 h-5 mr-2"></i>
                    Yeni Gider Ekle
                </button>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Toplam Gelir</p>
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        ₺<?php echo number_format($total_income, 2); ?></h3>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl">
                            <i data-lucide="trending-down" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Toplam Gider</p>
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        ₺<?php echo number_format($total_expenses, 2); ?></h3>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <i data-lucide="wallet" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Net Kar</p>
                    <h3
                        class="text-2xl font-bold <?php echo $net_profit >= 0 ? 'text-zinc-900 dark:text-white' : 'text-red-600 dark:text-red-400'; ?>">
                        ₺<?php echo number_format($net_profit, 2); ?></h3>
                </div>
            </div>

            <!-- Expense List -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
                <div class="p-6 border-b border-slate-200 dark:border-zinc-800">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Gider Kayıtları</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Kategori</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Açıklama</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Tarih</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">
                                Tutar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Henüz gider
                                    kaydı bulunmuyor.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $expense): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-slate-400 uppercase">
                                            <?php echo htmlspecialchars($expense['category'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        <?php echo htmlspecialchars($expense['description'] ?? ''); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-500">
                                        <?php echo isset($expense['expense_date']) ? date('d.m.Y', strtotime($expense['expense_date'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400">
                                        ₺<?php echo number_format($expense['amount'] ?? 0, 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Expense Modal -->
    <div id="addExpenseModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Yeni Gider Ekle</h3>
                <button onclick="document.getElementById('addExpenseModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="accounting.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_expense" value="1">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                    <select name="category" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                        <option value="Yazılım/Araç">Yazılım/Araç</option>
                        <option value="Pazarlama">Pazarlama</option>
                        <option value="Ofis/Kira">Ofis/Kira</option>
                        <option value="Vergi">Vergi</option>
                        <option value="Diğer">Diğer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tutar (₺)</label>
                    <input type="number" step="0.01" name="amount" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tarih</label>
                    <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Açıklama</label>
                    <textarea name="description" rows="2"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
