<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pages = [
            'hakkimizda' => [
                'title' => 'Hakkımızda',
                'content' => '<h2>Biz Kimiz?</h2><p>Patenli Ayakkabılar olarak, çocukların eğlenirken güvende olmasını sağlamak ve onlara unutulmaz bir deneyim yaşatmak için yola çıktık. 2026 yılında kurulan şirketimiz, yenilikçi ve kaliteli ışıklı, tekerlekli ayakkabı modellerini Türkiye\'nin dört bir yanındaki miniklerle buluşturmaktadır.</p><p>Misyonumuz, ebeveynlerin güvenle tercih edebileceği, çocukların ise yüzünde kocaman bir gülümsemeyle giyeceği ürünler sunmaktır. Ürünlerimizin tamamı uluslararası güvenlik standartlarına uygun olarak üretilmekte ve titiz testlerden geçirilmektedir.</p><h2>Neden Bizi Seçmelisiniz?</h2><ul><li><strong>Güvenlik Önceliğimiz:</strong> Tüm modellerimiz ekstra dayanıklı malzemeler ve güvenli frenleme sistemleri ile donatılmıştır.</li><li><strong>Hızlı Teslimat:</strong> Siparişlerinizi en hızlı şekilde, özenle paketleyerek kapınıza kadar ulaştırıyoruz.</li><li><strong>Müşteri Memnuniyeti:</strong> 7/24 aktif müşteri hizmetlerimizle satış öncesi ve sonrası her zaman yanınızdayız.</li></ul>'
            ],
            'gizlilik-politikasi' => [
                'title' => 'Gizlilik Politikası',
                'content' => '<h2>Kişisel Verilerin Korunması</h2><p>Patenli Ayakkabılar (Bundan böyle "Şirket" olarak anılacaktır), müşterilerimizin kişisel verilerinin gizliliğine ve güvenliğine son derece önem vermektedir. 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) uyarınca, web sitemiz (patenliayakkabilar.com) üzerinden bizimle paylaştığınız tüm kişisel verileriniz büyük bir hassasiyetle korunmakta ve yalnızca size daha iyi hizmet verebilmek amacıyla işlenmektedir.</p><h2>Toplanan Veriler ve Kullanım Amacı</h2><p>Sipariş süreçlerini yönetmek, kargo işlemlerini gerçekleştirmek ve kampanyalarımızdan sizleri haberdar etmek amacıyla ad, soyad, adres, e-posta ve telefon numarası gibi temel bilgilerinizi toplamaktayız. Kredi kartı ve ödeme bilgileriniz kesinlikle sunucularımızda saklanmaz, doğrudan güvenli ödeme altyapısı (BDDK onaylı kuruluşlar) üzerinden işlenir.</p><h2>Çerezler (Cookies)</h2><p>Alışveriş deneyiminizi iyileştirmek için çerezler kullanmaktayız. Tarayıcı ayarlarınızdan çerez kullanımını dilediğiniz zaman sınırlandırabilirsiniz.</p>'
            ],
            'iade-ve-degisim' => [
                'title' => 'İade ve Değişim Koşulları',
                'content' => '<h2>Kolay İade ve Değişim</h2><p>Satın almış olduğunuz ürünleri, teslimat tarihinden itibaren <strong>14 gün içerisinde</strong> hiçbir gerekçe göstermeksizin iade edebilir veya numara/model değişimi talep edebilirsiniz.</p><h2>İade/Değişim Şartları</h2><ul><li>Ürünün kullanılmamış, etiketlerinin koparılmamış ve orijinal kutusunun zarar görmemiş olması gerekmektedir.</li><li>Dışarıda (sokak, asfalt vb.) kullanılmış, tekerlekleri aşınmış veya çizilmiş ürünlerin iadesi hijyen ve yeniden satılabilirlik kuralları gereği kabul edilmemektedir. Sadece ev içinde (halı üzerinde) numara denemesi yapılmalıdır.</li><li>İade kargo ücretleri, anlaşmalı kargo kodumuz ile gönderildiği takdirde şirketimize aittir.</li></ul><p>İade veya değişim talebi oluşturmak için "Sipariş Takip" sayfasından işleminizi başlatabilir veya müşteri hizmetlerimizle iletişime geçebilirsiniz.</p>'
            ],
            'mesafeli-satis-sozlesmesi' => [
                'title' => 'Mesafeli Satış Sözleşmesi',
                'content' => '<h2>Madde 1 - Taraflar</h2><p>İşbu sözleşme, bir tarafta patenliayakkabilar.com web sitesini işleten Satıcı ile diğer tarafta site üzerinden sipariş veren Alıcı (Müşteri) arasında dijital ortamda onaylanarak yürürlüğe girmiştir.</p><h2>Madde 2 - Konu</h2><p>İşbu sözleşmenin konusu, Alıcı\'nın Satıcı\'ya ait web sitesinden elektronik ortamda siparişini yaptığı aşağıda nitelikleri ve satış fiyatı belirtilen ürünün satışı ve teslimi ile ilgili olarak 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p><h2>Madde 3 - Teslimat</h2><p>Ürün, Alıcı\'nın sipariş formunda belirttiği teslimat adresine, faturası ile birlikte paketlenmiş ve sağlam olarak en geç 3 iş günü içinde kargoya teslim edilir. Kargo firmasından kaynaklanan gecikmelerden Satıcı sorumlu tutulamaz.</p>'
            ],
            'sikca-sorulan-sorular' => [
                'title' => 'Sıkça Sorulan Sorular',
                'content' => '<h2>Sipariş ve Kargo</h2><p><strong>Siparişim ne zaman kargoya verilir?</strong><br>Saat 14:00\'e kadar verilen siparişler aynı gün, 14:00\'ten sonra verilen siparişler ise ertesi iş günü kargoya teslim edilmektedir.</p><p><strong>Hangi kargo şirketi ile çalışıyorsunuz?</strong><br>Türkiye\'nin her yerine Yurtiçi Kargo ve Aras Kargo güvencesiyle teslimat yapmaktayız.</p><h2>Ürün Kullanımı</h2><p><strong>Patenli ayakkabılar normal ayakkabı olarak kullanılabilir mi?</strong><br>Evet! Tüm modellerimizin tabanındaki gizli mekanizma sayesinde, tekerleği içeri gizleyerek ürünü günlük normal bir spor ayakkabı gibi kullanabilirsiniz. Arka kısımdaki butona basmanız yeterlidir.</p><p><strong>Işıkların şarjı ne kadar dayanır? Nasıl şarj edilir?</strong><br>Şarjlı modellerimiz kutu içerisinden çıkan çift uçlu USB kablo ile şarj edilir. Yaklaşık 2 saatlik şarj ile ortalama 6-8 saat boyunca aralıksız ışık yanabilmektedir.</p>'
            ]
        ];

        foreach ($pages as $slug => $data) {
            $page = \App\Models\Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $data['title']]
            );
            $page->content = $data['content'];
            $page->is_active = true;
            $page->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugs = ['hakkimizda', 'gizlilik-politikasi', 'iade-ve-degisim', 'mesafeli-satis-sozlesmesi', 'sikca-sorulan-sorular'];
        \App\Models\Page::whereIn('slug', $slugs)->delete();
    }
};
