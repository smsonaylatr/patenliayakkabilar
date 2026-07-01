<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '
                    <h2>Biz Kimiz?</h2>
                    <p><strong>Patenli Ayakkabılar</strong>, çocukların enerjisini sokağa güvenle taşımasını hedefleyen, hem şık bir spor ayakkabı hem de eğlenceli bir paten deneyimini tek bir üründe birleştiren yenilikçi bir markadır.</p>
                    <p>Amacımız, çocukların dışarıda daha çok vakit geçirmesini, spor yaparken eğlenmesini ve ebeveynlerin de çocuklarının güvenliğinden emin olmasını sağlamaktır. Yılların getirdiği tecrübe ile tasarladığımız tekerlekli ayakkabılarımız, dayanıklı malzemeleri ve konforlu iç yapısıyla öne çıkmaktadır.</p>
                    <h3>Misyonumuz</h3>
                    <p>Kaliteli, güvenilir ve çocukların yüzünü güldürecek yenilikçi ürünler sunarak, her adımda eğlenceyi garanti altına almak.</p>
                ',
                'is_active' => true,
            ],
            [
                'title' => 'Sıkça Sorulan Sorular',
                'slug' => 'sikca-sorulan-sorular',
                'content' => '
                    <h3>1. Patenli ayakkabıları normal ayakkabı olarak kullanabilir miyim?</h3>
                    <p>Evet! Ayakkabılarımızın altında bulunan özel mekanizma sayesinde tekerlekleri tek bir tuşla içeri gizleyebilir ve normal bir spor ayakkabı gibi yürüyebilirsiniz.</p>
                    <h3>2. Kaç yaşına kadar çocuklar kullanabilir?</h3>
                    <p>Ürünlerimiz genellikle 5 yaş ve üzeri çocuklar için uygundur. Numaralarımız 28\'den başlayıp 40 numaraya kadar çıkabilmektedir.</p>
                    <h3>3. Güvenlik için ekstra bir ekipman gerekiyor mu?</h3>
                    <p>Paten modunda kullanırken, tıpkı standart patenlerde olduğu gibi dizlik, dirseklik ve kask takılmasını kesinlikle tavsiye ediyoruz.</p>
                    <h3>4. Ürünler ne kadar sürede kargoya verilir?</h3>
                    <p>Siparişleriniz genellikle 24 saat içerisinde kargoya teslim edilmekte olup, bulunduğunuz şehre göre 1-3 iş günü içerisinde size ulaşmaktadır.</p>
                ',
                'is_active' => true,
            ],
            [
                'title' => 'İade ve Değişim',
                'slug' => 'iade-ve-degisim',
                'content' => '
                    <h2>İade ve Değişim Şartları</h2>
                    <p>Müşteri memnuniyeti bizim için en ön plandadır. Eğer satın aldığınız üründen memnun kalmazsanız veya numara uymama gibi bir durum yaşarsanız, <strong>14 gün içerisinde</strong> koşulsuz iade ve değişim hakkınızı kullanabilirsiniz.</p>
                    <h3>İade Şartları:</h3>
                    <ul>
                        <li>Ürünün kullanılmamış, etiketinin koparılmamış ve tekrar satılabilir özelliğini yitirmemiş olması gerekmektedir.</li>
                        <li>Orijinal kutusu hasar görmemiş olmalıdır.</li>
                        <li>İade sürecini başlatmak için <strong>Sipariş Takip</strong> sayfasından iade talebi oluşturabilirsiniz.</li>
                    </ul>
                    <p>Değişim işlemlerinde ise kargo ücreti karşılıklı olarak ücretsizdir.</p>
                ',
                'is_active' => true,
            ],
            [
                'title' => 'Mesafeli Satış Sözleşmesi',
                'slug' => 'mesafeli-satis-sozlesmesi',
                'content' => '
                    <h2>Mesafeli Satış Sözleşmesi</h2>
                    <p><strong>Madde 1: Taraflar</strong></p>
                    <p>Bu sözleşme, bir tarafta Patenli Ayakkabılar (bundan sonra "SATICI" olarak anılacaktır) ile diğer tarafta ürünü satın alan tüketici (bundan sonra "ALICI" olarak anılacaktır) arasında elektronik ortamda akdedilmiştir.</p>
                    <p><strong>Madde 2: Sözleşmenin Konusu</strong></p>
                    <p>İşbu sözleşmenin konusu, ALICI\'nın SATICI\'ya ait internet sitesinden elektronik ortamda siparişini yaptığı ürünün satışı ve teslimi ile ilgili olarak 6502 sayılı Tüketicinin Korunması Hakkında Kanun hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>
                    <p><strong>Madde 3: Cayma Hakkı</strong></p>
                    <p>ALICI, ürünü teslim aldığı tarihten itibaren 14 (on dört) gün içinde hiçbir gerekçe göstermeksizin cayma hakkına sahiptir.</p>
                ',
                'is_active' => true,
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '
                    <h2>Gizlilik ve Kişisel Verilerin Korunması Politikası</h2>
                    <p>Patenli Ayakkabılar olarak kişisel verilerinizin güvenliğine büyük önem veriyoruz. Sitemizden alışveriş yaparken paylaştığınız kişisel bilgiler, yalnızca sipariş sürecinin yönetilmesi ve sizlere daha iyi bir hizmet sunulması amacıyla kullanılmaktadır.</p>
                    <h3>Veri Paylaşımı</h3>
                    <p>Ad, soyad, adres ve iletişim bilgileriniz sadece anlaşmalı olduğumuz kargo firması ile (siparişin teslimi için) paylaşılmaktadır. Üçüncü şahıslara veya reklam şirketlerine bilgi satışı kesinlikle yapılmamaktadır.</p>
                    <h3>Güvenlik Önlemleri</h3>
                    <p>Sitemiz SSL sertifikası ile korunmakta olup, ödeme işlemleri sırasında girilen kredi kartı bilgileri sistemlerimizde kesinlikle saklanmamaktadır.</p>
                ',
                'is_active' => true,
            ],
            [
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => '
                    <h2>Bize Ulaşın</h2>
                    <p>Sorularınız, önerileriniz veya destek talepleriniz için bize aşağıdaki kanallardan ulaşabilirsiniz:</p>
                    <p><strong>Adres:</strong> Örnek Mahallesi, Paten Caddesi, No: 123, Kadıköy / İstanbul</p>
                    <p><strong>Müşteri Hizmetleri (WhatsApp):</strong> 0850 123 45 67</p>
                    <p><strong>E-posta:</strong> destek@patenliayakkabilar.com</p>
                    <p><strong>Çalışma Saatlerimiz:</strong> Hafta içi her gün 09:00 - 18:00</p>
                    <p>Ayrıca sağ alt köşede bulunan WhatsApp destek hattımızdan bize anında ulaşabilirsiniz!</p>
                ',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'is_active' => $page['is_active'],
                ]
            );
        }
    }
}
