# EkinCRM - Modern Müşteri ve Proje Yönetim Sistemi

EkinCRM, küçük ve orta ölçekli işletmeler için tasarlanmış, modern arayüzlü ve kullanıcı dostu bir CRM (Müşteri İlişkileri Yönetimi) sistemidir. Projelerinizi, müşterilerinizi ve finansal süreçlerinizi tek bir merkezden yönetmenize olanak tanır.

![EkinCRM Dashboard](https://github.com/mekroket/Ekin-CRM/blob/main/4.png) <!-- Buraya gerçek bir ekran görüntüsü eklenebilir -->

## 🚀 Özellikler

- **Karanlık Mod Desteği:** Göz yorgunluğunu azaltan modern ve şık karanlık tema.
- **Dashboard:** Şirketinizin genel durumunu (toplam müşteri, aktif projeler, gelir vb.) anlık olarak görün.
- **Müşteri Yönetimi:** Müşteri bilgilerini kaydedin, düzenleyin ve geçmişlerini takip edin.
- **Proje Yönetimi:** Projelerinizi bütçe ve teslim tarihleriyle birlikte yönetin.
- **Kanban Panosu:** Görevlerinizi sürükle-bırak yöntemiyle "Bekliyor", "Devam Ediyor" ve "Bitti" sütunları arasında taşıyın.
- **Ödeme Takibi:** Ödemelerinizi izleyin, profesyonel PDF teklifleri ve faturalar oluşturun.
- **Muhasebe:** Gelir ve giderlerinizi kaydederek karlılığınızı takip edin.
- **Responsive Tasarım:** Tüm cihazlarda (masaüstü, tablet, mobil) kusursuz çalışma.

## 🛠️ Teknolojiler

- **Backend:** PHP 8.x
- **Veritabanı:** MySQL (PDO)
- **Frontend:** Tailwind CSS, Lucide Icons, SortableJS
- **PDF:** Özel HTML-to-PDF motoru

## 📦 Kurulum

1. Bu depoyu klonlayın:
   ```bash
   git clone https://github.com/mekroket/Ekin-CRM.git
   ```
2. Veritabanını oluşturun ve `schema.sql` dosyasını içe aktarın.
3. `db.php.example` dosyasının adını `db.php` olarak değiştirin ve veritabanı bilgilerinizi girin:
   ```php
   $host = 'localhost';
   $db   = 'crm_sistemi';
   $user = 'root';
   $pass = 'sifreniz';
   ```
4. Projeyi WAMP, XAMPP veya benzeri bir PHP sunucusunda çalıştırın.

## 📸 Ekran Görüntüleri

| Dashboard | Kanban Panosu | Karanlık Mod |
|-----------|---------------|--------------|
| ![Dashboard](https://via.placeholder.com/200x150) | ![Kanban](https://via.placeholder.com/200x150) | ![Dark Mode](https://via.placeholder.com/200x150) |

## 📄 Lisans

Bu proje MIT lisansı ile lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakabilirsiniz.

---
Geliştiren: [mekroket](https://github.com/mekroket)
