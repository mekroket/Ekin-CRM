<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT py.*, p.title as project_title, p.description as project_desc, c.name as client_name, c.company as client_company 
                       FROM payments py 
                       JOIN projects p ON py.project_id = p.id 
                       JOIN clients c ON p.client_id = c.id 
                       WHERE py.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    exit('Kayıt bulunamadı.');
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif Hazırla - EkinCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-slate-200 py-4 px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="../payments.php" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-slate-500"></i>
                </a>
                <h1 class="text-xl font-bold text-slate-800">Teklif Hazırla</h1>
            </div>
        </header>

        <main class="flex-1 p-8 max-w-5xl mx-auto w-full">
            <form action="generate_pdf.php" method="POST" target="_blank" class="space-y-8">
                <input type="hidden" name="payment_id" value="<?php echo $id; ?>">

                <!-- Info Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-indigo-600"></i>
                        Proje Bilgileri
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="block text-slate-500 mb-1">Müşteri</span>
                            <span class="font-medium"><?php echo htmlspecialchars($data['client_name']); ?>
                                (<?php echo htmlspecialchars($data['client_company']); ?>)</span>
                        </div>
                        <div>
                            <span class="block text-slate-500 mb-1">Proje</span>
                            <span class="font-medium"><?php echo htmlspecialchars($data['project_title']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            <i data-lucide="list" class="w-5 h-5 text-indigo-600"></i>
                            Hizmet Kalemleri
                        </h2>
                        <button type="button" onclick="addItem()"
                            class="text-sm text-indigo-600 font-medium hover:text-indigo-700 flex items-center gap-1">
                            <i data-lucide="plus" class="w-4 h-4"></i> Kalem Ekle
                        </button>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <!-- Default Item -->
                        <div
                            class="item-row grid grid-cols-12 gap-4 items-start bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="col-span-6">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Açıklama</label>
                                <input type="text" name="items[0][name]"
                                    value="<?php echo htmlspecialchars($data['project_title']); ?> - Profesyonel Hizmet Bedeli"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Birim Fiyat</label>
                                <input type="number" step="0.01" name="items[0][price]"
                                    value="<?php echo $data['amount']; ?>" oninput="calculateTotal()"
                                    class="price-input w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Adet</label>
                                <input type="number" name="items[0][qty]" value="1" oninput="calculateTotal()"
                                    class="qty-input w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                            <div class="col-span-2 flex items-center justify-between pt-6">
                                <span class="font-bold text-slate-700 row-total">₺0.00</span>
                                <button type="button" onclick="removeItem(this)"
                                    class="text-slate-400 hover:text-red-600 transition-colors" title="Sil">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-slate-100 pt-4">
                        <div class="text-right">
                            <span class="block text-slate-500 text-sm">Genel Toplam</span>
                            <span id="grand-total" class="text-2xl font-bold text-indigo-600">₺0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="../payments.php"
                        class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-medium hover:bg-slate-50 transition-colors">İptal</a>
                    <button type="submit"
                        class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        PDF Oluştur
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        lucide.createIcons();
        let itemCount = 1;

        function calculateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const total = price * qty;
                row.querySelector('.row-total').textContent = '₺' + total.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                grandTotal += total;
            });
            document.getElementById('grand-total').textContent = '₺' + grandTotal.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function addItem() {
            const container = document.getElementById('items-container');
            const div = document.createElement('div');
            div.className = 'item-row grid grid-cols-12 gap-4 items-start bg-slate-50 p-4 rounded-xl border border-slate-200';
            div.innerHTML = `
                <div class="col-span-6">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Açıklama</label>
                    <input type="text" name="items[${itemCount}][name]" placeholder="Hizmet/Ürün adı" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Birim Fiyat</label>
                    <input type="number" step="0.01" name="items[${itemCount}][price]" value="0" oninput="calculateTotal()" class="price-input w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Adet</label>
                    <input type="number" name="items[${itemCount}][qty]" value="1" oninput="calculateTotal()" class="qty-input w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>
                <div class="col-span-2 flex items-center justify-between pt-6">
                    <span class="font-bold text-slate-700 row-total">₺0.00</span>
                    <button type="button" onclick="removeItem(this)" class="text-slate-400 hover:text-red-600 transition-colors" title="Sil">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
            lucide.createIcons();
            itemCount++;
            calculateTotal();
        }

        function removeItem(btn) {
            if (document.querySelectorAll('.item-row').length > 1) {
                btn.closest('.item-row').remove();
                calculateTotal();
            } else {
                alert('En az bir kalem olmalıdır.');
            }
        }

        // Initial calculation
        calculateTotal();
    </script>
</body>

</html>