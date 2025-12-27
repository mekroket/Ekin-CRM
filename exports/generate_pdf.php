<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    exit('Yetkisiz erişim.');
}
require_once '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT py.*, p.title as project_title, p.description as project_desc, c.name as client_name, c.company as client_company, c.email as client_email, c.phone as client_phone, c.address as client_address 
                       FROM payments py 
                       JOIN projects p ON py.project_id = p.id 
                       JOIN clients c ON p.client_id = c.id 
                       WHERE py.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    exit('Kayıt bulunamadı.');
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

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Teklif - <?php echo htmlspecialchars($data['project_title'] ?? ''); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-print-color-adjust: exact;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                background-color: white;
            }
            .container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 p-4 md:p-12">
    <div class="max-w-4xl mx-auto no-print mb-6 flex justify-end">
        <button onclick="window.print()"
            class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg  hover:bg-indigo-700 transition-all flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Yazdır / PDF Olarak Kaydet
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-sm overflow-hidden border-t-[16px] border-indigo-600 container">
        <div class="p-12">
            <!-- Header Section -->
            <div class="flex justify-between items-start mb-16">
                <div>
                    <h1 class="text-4xl font-black text-indigo-600 mb-4 tracking-tighter uppercase">
                        <?php echo htmlspecialchars($settings['company_name'] ?? 'EkinCRM'); ?>
                    </h1>
                    <div class="text-sm text-slate-500 space-y-1 max-w-xs">
                        <p class="font-medium text-slate-700"><?php echo nl2br(htmlspecialchars($settings['address'] ?? '')); ?></p>
                        <p>Tel: <?php echo htmlspecialchars($settings['phone'] ?? ''); ?></p>
                        <p>E-posta: <?php echo htmlspecialchars($settings['email'] ?? ''); ?></p>
                        <p>V.D: <?php echo htmlspecialchars($settings['tax_office'] ?? ''); ?> / No: <?php echo htmlspecialchars($settings['tax_no'] ?? ''); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-5xl font-light text-slate-200 uppercase tracking-[0.2em] mb-6">TEKLİF</h2>
                    <div class="space-y-1">
                        <p class="text-sm font-bold text-zinc-900">Teklif No: <span class="font-normal text-slate-500">#OFF-<?php echo str_pad($data['id'], 5, '0', STR_PAD_LEFT); ?></span></p>
                        <p class="text-sm font-bold text-zinc-900">Tarih: <span class="font-normal text-slate-500"><?php echo date('d.m.Y'); ?></span></p>
                        <p class="text-sm font-bold text-zinc-900">Geçerlilik: <span class="font-normal text-slate-500"><?php echo date('d.m.Y', strtotime('+15 days')); ?></span></p>
                    </div>
                </div>
            </div>

            <!-- Client & Project Info -->
            <div class="grid grid-cols-2 gap-12 mb-16">
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100">
                    <h3 class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-4">Müşteri Bilgileri</h3>
                    <div class="space-y-2">
                        <p class="text-xl font-bold text-zinc-900"><?php echo htmlspecialchars($data['client_name'] ?? ''); ?></p>
                        <p class="text-sm font-medium text-slate-600"><?php echo htmlspecialchars($data['client_company'] ?? ''); ?></p>
                        <p class="text-sm text-slate-500"><?php echo nl2br(htmlspecialchars($data['client_address'] ?? 'Adres belirtilmemiş.')); ?></p>
                        <div class="pt-2 text-xs text-slate-400 space-y-1">
                            <p><?php echo htmlspecialchars($data['client_email'] ?? ''); ?></p>
                            <p><?php echo htmlspecialchars($data['client_phone'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <h3 class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-4">Proje Özeti</h3>
                    <p class="text-2xl font-bold text-zinc-900 mb-3"><?php echo htmlspecialchars($data['project_title'] ?? ''); ?></p>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($data['project_desc'] ?? 'Proje detayları aşağıda belirtilmiştir.')); ?>
                    </p>
                </div>
            </div>

            <!-- Services Table -->
            <div class="mb-16">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-zinc-900">
                            <th class="py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hizmet Açıklaması</th>
                            <th class="py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest w-32">Birim Fiyat</th>
                            <th class="py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest w-24">Adet</th>
                            <th class="py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest w-32">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-8">
                                <p class="font-bold text-zinc-900 text-lg"><?php echo htmlspecialchars($data['project_title'] ?? ''); ?></p>
                                <p class="text-sm text-slate-500 mt-1">Profesyonel hizmet ve danışmanlık bedeli.</p>
                            </td>
                            <td class="py-8 text-right text-slate-600 font-medium">₺<?php echo number_format($data['amount'], 2); ?></td>
                            <td class="py-8 text-right text-slate-600 font-medium">1</td>
                            <td class="py-8 text-right font-bold text-zinc-900 text-lg">₺<?php echo number_format($data['amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="flex justify-end mb-24">
                <div class="w-64 space-y-3">
                    <div class="flex justify-between items-center text-slate-500 text-sm">
                        <span>Ara Toplam</span>
                        <span class="font-semibold text-zinc-900">₺<?php echo number_format($data['amount'], 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500 text-sm">
                        <span>KDV (%0)</span>
                        <span class="font-semibold text-zinc-900">₺0.00</span>
                    </div>
                    <div class="pt-4 border-t-2 border-zinc-900 flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-widest text-zinc-900">Genel Toplam</span>
                        <span class="text-2xl font-black text-indigo-600">₺<?php echo number_format($data['amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Notes & Footer -->
            <div class="grid grid-cols-2 gap-12 pt-12 border-t border-slate-100">
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Önemli Notlar</h4>
                    <ul class="text-[11px] text-slate-500 space-y-2 list-disc pl-4">
                        <li>Teklifin geçerlilik süresi oluşturulma tarihinden itibaren 15 gündür.</li>
                        <li>Ödemeler teklifte belirtilen banka hesabına yapılmalıdır.</li>
                        <li>Fiyatlara aksi belirtilmedikçe KDV dahil değildir.</li>
                    </ul>
                </div>
                <div class="text-right flex flex-col justify-end">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Bu belge dijital olarak oluşturulmuştur</p>
                    <p class="text-xs font-bold text-zinc-900"><?php echo htmlspecialchars($settings['company_name'] ?? 'EkinCRM'); ?> Sistemi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto mt-8 text-center no-print">
        <p class="text-slate-400 text-xs uppercase tracking-widest">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['company_name'] ?? 'EkinCRM'); ?> - Tüm Hakları Saklıdır.</p>
    </div>
</body>

</html>
