<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: payments.php');
    exit;
}
require_once '../includes/db.php';

$payment_id = $_GET['id'];

// Ödeme, Proje, Müşteri bilgilerini çek
$stmt = $pdo->prepare("SELECT py.*, p.title as project_title, c.name as client_name, c.address as client_address, c.tax_office as client_tax_office, c.tax_no as client_tax_no 
                       FROM payments py 
                       JOIN projects p ON py.project_id = p.id 
                       JOIN clients c ON p.client_id = c.id 
                       WHERE py.id = ?");
$stmt->execute([$payment_id]);
$payment = $stmt->fetch();

if (!$payment) {
    header('Location: payments.php');
    exit;
}

// Şirket ayarlarını çek
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch() ?: [
    'company_name' => 'EkinCRM',
    'address' => '',
    'phone' => '',
    'email' => '',
    'tax_office' => '',
    'tax_no' => '',
    'iban' => ''
];

// KDV Hesaplama (Örnek %20)
$tax_rate = 0.20;
$subtotal = $payment['amount'];
$tax_amount = $subtotal * $tax_rate;
$total_amount = $subtotal + $tax_amount;
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Fatura - <?php echo $payment['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .invoice-box {
                border: none;
                shadow: none;
            }
        }
    </style>
</head>

<body class="bg-slate-100 p-8">
    <div class="max-w-4xl mx-auto no-print mb-4 flex justify-end">
        <button onclick="window.print()"
            class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition-all">
            Yazdır / PDF Kaydet
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 shadow-2xl rounded-sm invoice-box border-t-[12px] border-indigo-600">
        <!-- Header / Antetli Kısım -->
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-3xl font-black text-indigo-600 mb-2 uppercase tracking-tighter">
                    <?php echo htmlspecialchars($settings['company_name'] ?? 'EkinCRM'); ?></h1>
                <div class="text-sm text-slate-500 space-y-1">
                    <p><?php echo nl2br(htmlspecialchars($settings['address'] ?? '')); ?></p>
                    <p>Tel: <?php echo htmlspecialchars($settings['phone'] ?? ''); ?></p>
                    <p>E-posta: <?php echo htmlspecialchars($settings['email'] ?? ''); ?></p>
                    <p>V.D: <?php echo htmlspecialchars($settings['tax_office'] ?? ''); ?> / No:
                        <?php echo htmlspecialchars($settings['tax_no'] ?? ''); ?></p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-light text-slate-300 uppercase tracking-widest mb-4">FATURA</h2>
                <div class="text-sm">
                    <p class="font-bold text-zinc-900">Fatura No: <span
                            class="font-normal text-slate-600">#INV-<?php echo str_pad($payment['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </p>
                    <p class="font-bold text-zinc-900">Tarih: <span
                            class="font-normal text-slate-600"><?php echo date('d.m.Y'); ?></span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12 mb-12">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Sayın,</h3>
                <div class="text-zinc-900">
                    <p class="font-bold text-lg"><?php echo htmlspecialchars($payment['client_name'] ?? ''); ?></p>
                    <p class="text-sm text-slate-600">
                        <?php echo nl2br(htmlspecialchars($payment['client_address'] ?? 'Adres belirtilmemiş.')); ?></p>
                </div>
            </div>
            <div class="bg-slate-50 p-6 rounded-xl">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Ödeme Bilgileri</h3>
                <div class="text-sm space-y-1">
                    <p class="font-bold">Banka: <span
                            class="font-normal"><?php echo htmlspecialchars($settings['company_name'] ?? 'EkinCRM'); ?></span></p>
                    <p class="font-bold">IBAN: <span
                            class="font-normal"><?php echo htmlspecialchars($settings['iban'] ?? ''); ?></span></p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full mb-12">
            <thead>
                <tr class="border-b-2 border-zinc-900">
                    <th class="py-4 text-left text-sm font-bold uppercase tracking-wider">Açıklama</th>
                    <th class="py-4 text-right text-sm font-bold uppercase tracking-wider">Miktar</th>
                    <th class="py-4 text-right text-sm font-bold uppercase tracking-wider">Birim Fiyat</th>
                    <th class="py-4 text-right text-sm font-bold uppercase tracking-wider">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-6">
                        <p class="font-bold text-zinc-900"><?php echo htmlspecialchars($payment['project_title'] ?? ''); ?>
                        </p>
                        <p class="text-xs text-slate-500">Proje Hizmet Bedeli</p>
                    </td>
                    <td class="py-6 text-right text-slate-600">1</td>
                    <td class="py-6 text-right text-slate-600">₺<?php echo number_format($subtotal, 2); ?></td>
                    <td class="py-6 text-right font-bold text-zinc-900">₺<?php echo number_format($subtotal, 2); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-64 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Ara Toplam</span>
                    <span class="font-bold text-zinc-900">₺<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">KDV (%20)</span>
                    <span class="font-bold text-zinc-900">₺<?php echo number_format($tax_amount, 2); ?></span>
                </div>
                <div class="flex justify-between text-xl border-t-2 border-zinc-900 pt-3">
                    <span class="font-black uppercase tracking-tighter">GENEL TOPLAM</span>
                    <span class="font-black text-indigo-600">₺<?php echo number_format($total_amount, 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div
            class="mt-24 pt-12 border-t border-slate-100 text-center text-[10px] text-slate-400 uppercase tracking-widest">
            <p>Bu fatura EkinCRM sistemi tarafından oluşturulmuştur.</p>
        </div>
    </div>
</body>

</html>
