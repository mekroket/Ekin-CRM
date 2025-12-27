<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $tax_office = $_POST['tax_office'];
    $tax_no = $_POST['tax_no'];
    $iban = $_POST['iban'];

    $stmt = $pdo->prepare("UPDATE settings SET company_name = ?, address = ?, phone = ?, email = ?, tax_office = ?, tax_no = ?, iban = ? WHERE id = 1");
    $stmt->execute([$company_name, $address, $phone, $email, $tax_office, $tax_no, $iban]);
    $success = true;
}

$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch() ?: [
    'company_name' => 'EkinCRM',
    'address' => '',
    'phone' => '',
    'email' => '',
    'tax_office' => '',
    'tax_no' => '',
    'iban' => ''
];
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - EkinCRM</title>
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
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Sistem Ayarları</h1>
                <p class="text-slate-500 dark:text-slate-400">Şirket bilgilerinizi ve antetli kağıt ayarlarınızı buradan
                    yönetin.</p>
            </header>

            <?php if ($success): ?>
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 rounded-xl flex items-center transition-colors">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    Ayarlar başarıyla güncellendi.
                </div>
            <?php endif; ?>

            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
                <form action="settings.php" method="POST" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">Şirket
                                Adı (Antetli Kağıt İçin)</label>
                            <input type="text" name="company_name"
                                value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">Adres</label>
                            <textarea name="address" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">Telefon</label>
                            <input type="text" name="phone"
                                value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">E-posta</label>
                            <input type="email" name="email"
                                value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">Vergi
                                Dairesi</label>
                            <input type="text" name="tax_office"
                                value="<?php echo htmlspecialchars($settings['tax_office'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">Vergi
                                No</label>
                            <input type="text" name="tax_no"
                                value="<?php echo htmlspecialchars($settings['tax_no'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider text-[10px]">IBAN</label>
                            <input type="text" name="iban"
                                value="<?php echo htmlspecialchars($settings['iban'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                placeholder="TR00 0000 0000 0000 0000 0000 00">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-zinc-800 flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg  dark:shadow-indigo-900/20 transition-all flex items-center">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
