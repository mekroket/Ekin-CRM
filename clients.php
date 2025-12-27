<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';

// Müşteri Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company = $_POST['company'];
    $address = $_POST['address'];
    $tax_office = $_POST['tax_office'];
    $tax_no = $_POST['tax_no'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone, company, address, tax_office, tax_no, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $company, $address, $tax_office, $tax_no, $notes]);
    header('Location: clients.php?success=1');
    exit;
}

// Müşteri Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_client'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company = $_POST['company'];
    $address = $_POST['address'];
    $tax_office = $_POST['tax_office'];
    $tax_no = $_POST['tax_no'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, phone = ?, company = ?, address = ?, tax_office = ?, tax_no = ?, notes = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $company, $address, $tax_office, $tax_no, $notes, $id]);
    header('Location: clients.php?updated=1');
    exit;
}

// Müşteri Silme İşlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: clients.php?deleted=1');
    exit;
}

$clients = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">    <title>Müşteriler - EkinCRM</title>
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
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Müşteriler</h1>
                    <p class="text-slate-500 dark:text-slate-400">Müşteri portföyünüzü buradan yönetin.</p>
                </div>
                <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl flex items-center shadow-lg  transition-all">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Yeni Müşteri Ekle
                </button>
            </header>

            <!-- Client List -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Müşteri /
                                Şirket</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">İletişim</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Kayıt Tarihi
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">
                                İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Henüz
                                    müşteri bulunmuyor.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-white">
                                            <?php echo htmlspecialchars($client['name'] ?? ''); ?>
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($client['company'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-zinc-900 dark:text-slate-300">
                                            <?php echo htmlspecialchars($client['email'] ?? ''); ?>
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($client['phone'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        <?php echo isset($client['created_at']) ? date('d.m.Y', strtotime($client['created_at'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button onclick='editClient(<?php echo json_encode($client); ?>)'
                                            class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <i data-lucide="edit-2" class="w-5 h-5"></i>
                                        </button>
                                        <a href="?delete=<?php echo $client['id']; ?>"
                                            onclick="return confirm('Silmek istediğinize emin misiniz?')"
                                            class="text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors inline-block">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Client Modal -->
    <div id="addModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Yeni Müşteri Ekle</h3>
                <button onclick="document.getElementById('addModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="clients.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_client" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ad
                            Soyad</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-posta</label>
                        <input type="email" name="email"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon</label>
                        <input type="text" name="phone"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Şirket</label>
                        <input type="text" name="company"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Adres</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vergi
                            Dairesi</label>
                        <input type="text" name="tax_office"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vergi
                            No</label>
                        <input type="text" name="tax_no"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notlar</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                    </div>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Client Modal -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-200 dark:border-zinc-800 transition-colors">
            <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Müşteriyi Düzenle</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="clients.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="update_client" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ad
                            Soyad</label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-posta</label>
                        <input type="email" name="email" id="edit_email"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon</label>
                        <input type="text" name="phone" id="edit_phone"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Şirket</label>
                        <input type="text" name="company" id="edit_company"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Adres</label>
                        <textarea name="address" id="edit_address" rows="2"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vergi
                            Dairesi</label>
                        <input type="text" name="tax_office" id="edit_tax_office"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vergi
                            No</label>
                        <input type="text" name="tax_no" id="edit_tax_no"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notlar</label>
                        <textarea name="notes" id="edit_notes" rows="2"
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"></textarea>
                    </div>
                </div>
                <div class="pt-4 flex space-x-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">İptal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function editClient(client) {
            document.getElementById('edit_id').value = client.id;
            document.getElementById('edit_name').value = client.name || '';
            document.getElementById('edit_email').value = client.email || '';
            document.getElementById('edit_phone').value = client.phone || '';
            document.getElementById('edit_company').value = client.company || '';
            document.getElementById('edit_address').value = client.address || '';
            document.getElementById('edit_tax_office').value = client.tax_office || '';
            document.getElementById('edit_tax_no').value = client.tax_no || '';
            document.getElementById('edit_notes').value = client.notes || '';

            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</body>

</html>
