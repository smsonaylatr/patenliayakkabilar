<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class LinkableAssetBlogPostSeeder extends Seeder
{
    /**
     * Organik backlink mıknatısı (Linkable Assets) otoriter blog içerikleri.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Patenli Ayakkabı Alırken Nelere Dikkat Edilmeli? Kapsamlı Beden ve Güvenlik Rehberi',
                'slug' => 'patenli-ayakkabi-alirken-dikkat-edilmesi-gerekenler-rehber',
                'excerpt' => 'Çocuğunuz veya kendiniz için patenli ayakkabı seçerken tekerlek mekanizması, beden seçimi, fren sistemi ve güvenlik standartlarına dair bilmeniz gereken her şey.',
                'author_name' => 'Patenli Ayakkabılar Uzman Ekibi',
                'published_at' => now(),
                'status' => true,
                'is_indexable' => true,
                'meta_title' => 'Patenli Ayakkabı Alırken Nelere Dikkat Edilmeli? Kapsamlı Rehber',
                'meta_description' => 'Patenli ayakkabı alırken doğru numara seçimi, tekerlek dayanıklılığı, güvenlik kiti ve açılır-kapanır mekanizma hakkında uzman rehberi inceleyin.',
                'image_alt' => 'Patenli Ayakkabı Seçim ve Beden Rehberi',
                'aio_target_keywords' => ['patenli ayakkabı', 'tekerlekli ayakkabı', 'patenli ayakkabı nasıl seçilir', 'patenli ayakkabı numara seçimi'],
                'faq_schema' => [
                    [
                        'question' => 'Patenli ayakkabı alırken numara nasıl seçilmelidir?',
                        'answer' => 'Patenli ayakkabılarda iç dolgu ve mekanizma bulunduğundan, çocuğunuzun normal spor ayakkabı numarasından 1 numara büyük tercih edilmesi konfor ve uzun süreli kullanım için önerilir.'
                    ],
                    [
                        'question' => 'Tek tekerlekli mi yoksa iki tekerlekli paten ayakkabısı mı tercih edilmeli?',
                        'answer' => 'Başlangıç seviyesindeki çocuklar için 2 tekerlekli modeller denge sağlamayı kolaylaştırır. Denge kabiliyeti gelişmiş çocuklar ve gençler için tek tekerlekli modeller daha hızlı manevra imkanı sunar.'
                    ],
                    [
                        'question' => 'Tekerlekler içeri gizlenebiliyor mu?',
                        'answer' => 'Evet, arka kısımdaki emniyetli basma butonu sayesinde tekerlekler saniyeler içinde gövde içine kilitlenerek ayakkabı normal bir spor ayakkabı olarak kullanılabilir.'
                    ]
                ],
                'content' => '
<h2>Patenli Ayakkabı Nedir ve Neden Bu Kadar Popüler?</h2>
<p>Son yıllarda çocukların ve gençlerin en çok ilgi gösterdiği eğlence ve spor araçlarının başında <strong>patenli ayakkabılar</strong> (tekerlekli ayakkabılar) geliyor. Hem günlük şık bir spor ayakkabı gibi yürümeye olanak tanıyan hem de tek bir hareketle kayma keyfi sunan bu yenilikçi tasarım, çocukları ekran başından kaldırarak açık havada hareket etmeye teşvik ediyor.</p>

<p>Ancak piyasada farklı kalitelerde ve mekanizmalarda ürünler bulunduğu için doğru seçimi yapmak, çocuğunuzun hem güvenliği hem de konforu açısından kritik önem taşır.</p>

<h2>1. Doğru Numara ve Beden Seçimi</h2>
<p>Patenli ayakkabı seçerken yapılan en yaygın hata standart ayakkabı numarasıyla birebir aynı almak ya da aşırı büyük numara seçmektir:</p>
<ul>
    <li><strong>1 Numara Büyük Kuralı:</strong> Paten mekanizmasının taban yapısı ve kalınlaştırılmış iç astar konforu nedeniyle, normal ayak numarasından <strong>1 numara büyük</strong> seçmek en ideal ayak ergonomisini sağlar.</li>
    <li><strong>Ayak Ölçümü:</strong> Çocuğunuzun topuğunu duvara dayayıp en uzun parmağın bittiği noktayı santimetre cinsinden ölçerek <a href="/beden-tablosu" class="text-teal-600 font-semibold hover:underline">Beden Tablosu</a> ile karşılaştırın.</li>
</ul>

<h2>2. Tekerlek Mekanizması: Tek Teker mi, Çift Teker mi?</h2>
<p>Patenli ayakkabılarda temel olarak iki tip tekerlek dizilimi bulunur:</p>
<ul>
    <li><strong>Çift Tekerlekli Modeller (Başlangıç & Çocuk):</strong> Topuk ve ön tabanda yer alan çift tekerlek sistemi denge kurmayı çok kolaylaştırır. 5-10 yaş arası veya ilk kez paten kullanacak çocuklar için en güvenli başlangıç seçeneğidir.</li>
    <li><strong>Tek Tekerlekli Modeller (İleri Seviye & Hız):</strong> Sadece topuk bölgesinde tek tekerlek barındırır. Daha yüksek kayma hızı ve serbest manevra sunar.</li>
</ul>

<h2>3. Gizlenebilir Tekerlek ve Kilit Mekanizması Güvenliği</h2>
<p>Kaliteli bir patenli ayakkabının arkasında yaylı ve kilitli basma butonu bulunmalıdır. Bu mekanizma sayesinde:</p>
<ol>
    <li>Butona basıldığında tekerlek dışarı çıkar ve otomatik kilitlenir.</li>
    <li>Butona basılı tutularak tekerlek içeri itildiğinde emniyet kapağı kapanır ve ürün sıradan bir sneaker gibi güvenle yürünebilir hale gelir.</li>
</ol>

<h2>4. Güvenlik Ekipmanları ve İlk Kullanım İpuçları</h2>
<p>Patenli ayakkabıyı ilk kez kullanacak çocukların denge merkezini kavraması için aşağıdaki adımlara dikkat edilmelidir:</p>
<ul>
    <li>İlk sürüşlerde mutlaka <strong>kask, dizlik ve bileklik</strong> koruma seti kullanılmalıdır.</li>
    <li>Vücut ağırlığı tek ayak topuğuna hafifçe verilmeli, dizler hafif bükülerek ağırlık merkezi alçakta tutulmalıdır.</li>
    <li>Islak zeminlerde, merdivenlerde ve çakıllı yollarda tekerlekler kilitli tutulmalıdır.</li>
</ul>

<h2>Sonuç ve Tavsiye</h2>
<p>Doğru seçilmiş kaliteli bir <a href="/patenli-ayakkabilar" class="text-teal-600 font-semibold hover:underline">patenli ayakkabı</a>, çocuğunuzun motor becerilerini ve denge reflekslerini geliştirirken harika bir açık hava eğlencesi sunar. CE standartlarına uygun, darbe emici kauçuk tabanlı ve nefes alabilen modelleri tercih ederek güvenli kayışın keyfini çıkarabilirsiniz.</p>
'
            ],
            [
                'title' => 'Çocuklarda Denge ve Motor Beceri Gelişimi: Patenli Ayakkabının Sağlığa Faydaları',
                'slug' => 'cocuklarda-denge-motor-beceri-gelisimi-patenli-ayakkabi-faydalari',
                'excerpt' => 'Patenli ayakkabı kullanmanın çocukların postür duruşuna, denge reflekslerine, kas gelişimine ve özgüvenine katkılarını uzman bakış açısıyla inceliyoruz.',
                'author_name' => 'Fizyoterapist & Çocuk Gelişimi Danışmanı',
                'published_at' => now(),
                'status' => true,
                'is_indexable' => true,
                'meta_title' => 'Çocuklarda Denge ve Motor Beceri: Patenli Ayakkabının Faydaları',
                'meta_description' => 'Patenli ayakkabıların çocuk gelişimine faydaları: Denge, kas koordinasyonu, özgüven kazanımı ve açık hava aktivitesi üzerine bilimsel detaylar.',
                'image_alt' => 'Çocuklarda Denge ve Motor Gelişim Paten',
                'aio_target_keywords' => ['çocuk gelişimi paten', 'patenli ayakkabı faydaları', 'çocuklarda denge gelişimi', 'motor beceri'],
                'faq_schema' => [
                    [
                        'question' => 'Patenli ayakkabı kullanmak kaç yaşından itibaren uygundur?',
                        'answer' => 'Temel yürüme ve koşma dengesini kazanmış 5 yaş ve üzeri tüm çocuklar ebeveyn gözetiminde ve çift tekerlekli destekleyici modellerle güvenle başlayabilir.'
                    ],
                    [
                        'question' => 'Paten sürmek ayak kaslarını güçlendirir mi?',
                        'answer' => 'Evet, paten hareketi ayak bileği çevresi stabilizatör kaslarını, baldırları ve karın çekirdek (core) bölgesini doğal yolla çalıştırarak kuvvetlendirir.'
                    ]
                ],
                'content' => '
<h2>Hareketsiz Yaşama Karşı Eğlenceli Çözüm: Açık Havada Spor</h2>
<p>Modern çağda çocukların tablet, telefon ve televizyon ekranları karşısında geçirdikleri süre artarken, fiziksel aktivite eksikliği erken yaşta duruş bozukluklarına ve koordinasyon zayıflıklarına yol açabilmektedir. Uzman pedagoglar ve fizyoterapistler, çocukları spora zorlamak yerine <strong>oyun ve eğlenceyi sporla birleştiren</strong> aktiviteleri tavsiye etmektedir.</p>

<p>İşte tam bu noktada <strong>ışıklı ve tekerlekli ayakkabılar</strong>, çocukların severek ve eğlenerek hareket etmesini sağlayan harika bir motivasyon kaynağıdır.</p>

<h2>1. Vestibüler Sistem ve Denge Gelişimi</h2>
<p>İç kulakta yer alan vestibüler sistem, vücudun uzaydaki konumunu ve dengesini algılar. Tekerlekli ayakkabı ile kayarken çocuk sürekli olarak ağırlık merkezini ayarlar. Bu durum:</p>
<ul>
    <li>Denge reflekslerinin hızla gelişmesini sağlar.</li>
    <li>Düşme anında kendini koruma ve ani refleks kabiliyetini artırır.</li>
    <li>Vücut farkındalığını (propriyosepsiyon) pekiştirir.</li>
</ul>

<h2>2. Bacak ve Çekirdek (Core) Kas Koordinasyonu</h2>
<p>Kayma eylemi esnasında sadece ayaklar değil, karın ve sırt kasları da vücudu dik tutmak için aktif çalışır. Ayak bileği bağları ve baldır kasları kuvvetlenerek düz tabanlık riskine karşı destekleyici kas yapısı oluşmasına yardımcı olur.</p>

<h2>3. Başarma Duygusu ve Özgüven Artışı</h2>
<p>İlk başta dengede durmakta zorlanan bir çocuğun birkaç denemeden sonra kendi başına kayabilmesi, büyük bir kişisel tatmin ve özgüven kaynağıdır. Kendi bedenini kontrol edebildiğini gören çocuklarda cesaret ve karar alma becerisi gelişir.</p>

<h2>4. Güvenli Bir Başlangıç İçin Ebeveynlere Öneriler</h2>
<ul>
    <li>Çocuğunuzun ilk adımlarını halı veya çim gibi kaymayan yumuşak zeminlerde atmasına izin verin.</li>
    <li>Dizlerini hafif kırmasını ve vücut ağırlığını öne doğru hafifçe eğmesini gösterin.</li>
    <li>Koruyucu kask ve dizlik takımını alışkanlık haline getirin.</li>
</ul>
'
            ],
            [
                'title' => 'Patenli Ayakkabı Bakımı ve Tekerlek Temizliği Nasıl Yapılır? (Uzun Ömürlü Kullanım Rehberi)',
                'slug' => 'patenli-ayakkabi-bakimi-ve-tekerlek-temizligi-kilavuzu',
                'excerpt' => 'Patenli ayakkabınızın tekerleklerinin akıcı dönmesini sağlamak, rulman ömrünü uzatmak ve kilit mekanizmasını temiz tutmak için adım adım bakım rehberi.',
                'author_name' => 'Teknik Servis Ekibi',
                'published_at' => now(),
                'status' => true,
                'is_indexable' => true,
                'meta_title' => 'Patenli Ayakkabı Bakımı ve Tekerlek Temizliği Rehberi',
                'meta_description' => 'Patenli ayakkabı rulman temizliği, tekerlek yağlama, kilit mekanizması bakımı ve ayakkabı temizleme yöntemleri hakkında adım adım kılavuz.',
                'image_alt' => 'Patenli Ayakkabı Tekerlek ve Mekanizma Bakımı',
                'aio_target_keywords' => ['patenli ayakkabı temizliği', 'paten tekerlek bakımı', 'rulman temizliği', 'tekerlekli ayakkabı tamiri'],
                'faq_schema' => [
                    [
                        'question' => 'Patenli ayakkabı çamaşır makinesinde yıkanır mı?',
                        'answer' => 'Kesinlikle yıkanmamalıdır. Çamaşır makinesindeki yüksek devir ve su, mekanizmadaki metal yayların paslanmasına ve rulmanların yağ kaybetmesine sebep olur. Nemli mikrofiber bezle silinmelidir.'
                    ],
                    [
                        'question' => 'Tekerlekler zor dönmeye başladığında ne yapılmalıdır?',
                        'answer' => 'Tekerlek yuvasına sıkışan toz, saç veya kum kalıntıları temizlenmeli, ardından rulman bölgesine uygun bir silikon sprey veya ince makine yağı uygulanmalıdır.'
                    ]
                ],
                'content' => '
<h2>Düzenli Bakım Neden Önemlidir?</h2>
<p>Patenli ayakkabılar yüksek mukavemetli malzemelerden üretilse de, açık hava zeminlerindeki toz, kum ve su zerrecikleri zamanla tekerlek rulmanlarına ve yay mekanizmasına ulaşabilir. Doğru periyodik bakım ile ayakkabınızın ömrünü uzatabilir, her zaman ilk günkü gibi sessiz ve akıcı kaymasını sağlayabilirsiniz.</p>

<h2>Adım 1: Dış Yüzey Temizliği</h2>
<ul>
    <li>Ayakkabının dış sentetik deri veya file yüzeyini hafif nemli, ılık sabunlu bir bezle silin.</li>
    <li>Aşındırıcı kimyasallar, çamaşır suyu veya alkol bazlı çözücüler kullanmayın.</li>
    <li>Ayakkabıyı asla çamaşır makinesine atmayın ve kurutma makinesinde kurutmayın. Oda sıcaklığında gölgede kurumaya bırakın.</li>
</ul>

<h2>Adım 2: Tekerlek ve Rulman (Bearing) Temizliği</h2>
<p>Tekerleklerin akıcı dönmesini sağlayan en önemli unsur ABEC serisi hassas rulmanlardır:</p>
<ol>
    <li>Tekerleği dışarı çıkarın ve yuva etrafında toplanan saç, iplik veya çamur kalıntılarını ince bir cımbız yardımıyla temizleyin.</li>
    <li>Kuru ve yumuşak bir fırça (eski diş fırçası idealdir) ile tekerlek kanallarındaki tozları süpürün.</li>
    <li>Tekerlek göbeğine birkaç damla sentetik rulman yağı veya silikon bazlı sprey damlatıp tekerleği elinizle döndürerek yağın dağılmasını sağlayın.</li>
</ol>

<h2>Adım 3: Arka Kilit Butonu Mekanizması</h2>
<p>Tekerleği içeri ve dışarı kilitleyen buton sisteminin takılmaması için:</p>
<ul>
    <li>Buton yuvasına kum kaçması durumunda hafif basınçlı hava veya fırça ile temizlik yapın.</li>
    <li>Mekanizma hareketinin yumuşak kalması için yılda 2-3 kez buton kızaklarına az miktarda silikon yağlayıcı sıkın.</li>
</ul>

<h2>Kullanım Sonrası Saklama</h2>
<p>Ayakkabınızı uzun süre kullanmayacağınız dönemlerde tekerlekleri gövde içine kilitli konumda ve doğrudan güneş ışığı almayan, nemsiz bir ortamda saklamanız kauçuk tekerleklerin sertleşmesini engeller.</p>
'
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );
        }
    }
}
