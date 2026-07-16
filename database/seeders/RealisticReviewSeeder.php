<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;

class RealisticReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        if ($products->count() === 0) {
            $this->command->info('No products found to attach reviews.');
            return;
        }

        $names = [
            // Female names
            'Ayşe Yılmaz', 'Fatma Kaya', 'Zeynep Demir', 'Elif Çelik', 'Merve Şahin',
            'Esra Yıldız', 'Büşra Yıldırım', 'Ceren Öztürk', 'Selin Aydın', 'Tuğba Özdemir',
            'Gizem Arslan', 'Berna Doğan', 'Gamze Kılıç', 'Derya Aslan', 'Bahar Çetin',
            'Yasemin Kara', 'Dilek Koç', 'Burcu Kurt', 'Aylin Özkan', 'Cansu Şimşek',
            'Melis Polat', 'Ece Öz', 'Hande Korkmaz', 'İrem Çakır', 'Pelin Erdoğan',
            'Sedef Yavuz', 'Tuğçe Can', 'Pınar Acar', 'Özge Yalçın', 'Sibel Güneş',
            'Şeyma Bozkurt', 'Buse Bulut', 'Eda Keskin', 'Gözde Tekin', 'Hilal Çoban',
            'Rabia Erdoğan', 'Kübra Şen', 'Aslı Avcı', 'Nihan Gök', 'Sinem Uysal',
            'Serra Yalçın', 'Lara Coşkun', 'Defne Doğan', 'Eylül Kılıç', 'Derin Aslan',
            'Mira Güler', 'Azra Yıldız', 'Ada Kaplan',
            // Male names
            'Mehmet Kaya', 'Mustafa Demir', 'Ahmet Çelik', 'Ali Şahin', 'Hüseyin Yıldız',
            'Hasan Yıldırım', 'İbrahim Öztürk', 'Murat Aydın', 'Volkan Özdemir', 'Emre Arslan',
            'Burak Doğan', 'Gökhan Kılıç', 'Fatih Aslan', 'Hakan Çetin', 'Tolga Kara',
            'Oğuzhan Koç', 'Kemal Kurt', 'Enes Özkan', 'Yasin Şimşek', 'Serkan Polat',
            'Tarık Öz', 'Uğur Korkmaz', 'Erdem Çakır', 'Can Erdoğan', 'Kerem Yavuz',
            'Barış Can', 'Emir Acar', 'Cem Yalçın', 'Deniz Güneş', 'Koray Bozkurt',
            'Eren Bulut', 'Kaan Keskin', 'Mert Tekin', 'Yiğit Çoban', 'Arda Şen',
            'Bora Avcı', 'Ozan Gök', 'Sarp Uysal', 'Efe Kaya', 'Doruk Demir',
            'Alp Çelik', 'Caner Şahin', 'Turan Yıldız', 'Yusuf Yıldırım', 'Onur Öztürk',
            'Umut Aydın', 'Doğukan Özdemir'
        ];

        // 95 Unique, realistic reviews about roller shoes
        $comments = [
            "Kızım bayıldı, ayağından çıkarmak istemiyor. Renkleri de görseldeki gibi çok canlı.",
            "Tekerlek mekanizması çok pratik. Çocuğum tek hamlede patenden ayakkabıya çevirebiliyor.",
            "Oğlum için aldım. Kargolama hızı çok iyiydi. Teşekkür ederiz.",
            "Ayakkabının kalitesi beklediğimden çok daha iyi çıktı. Sadece kalıpları biraz dar, 1 numara büyük alınabilir.",
            "Hem ayakkabı hem paten olması harika bir tasarım. AVM'lerde gezerken çok pratik oluyor.",
            "Işıkları çok canlı yanıyor. Akşamları parkta kullanırken hem çok eğlenceli hem de güvenli.",
            "Kızımın doğum günü için hediye almıştım. Hayatında aldığı en güzel hediye olduğunu söyledi. Satıcıya çok teşekkürler.",
            "Ürün elime iki günde ulaştı. Paketleme özenliydi. Kullanımı da anlatıldığı kadar kolaymış.",
            "Tekerlekler gayet sağlam. Asfaltta sürerken hiç sarsmıyor.",
            "Dışarıda paten olarak, okulda ayakkabı olarak kullanıyor. Gerçekten tam bir fiyat performans ürünü.",
            "Beklediğimizden biraz daha ağır ama sanırım paten mekanizmasından kaynaklı. Oğlum çok sevdi.",
            "Rengi ve modeli fotoğraftakinden daha güzel duruyor canlıda. Tavsiye ederim.",
            "Alırken biraz tereddüt etmiştim ama geldiğinde kalitesini görünce iyiki almışım dedim.",
            "İç astarı yumuşacık, çocuğun ayağını vurmuyor. Konforlu bir ayakkabı.",
            "Yeğenime hediye aldım. Paketi açar açmaz evin içinde sürmeye başladı. Çok eğlenceli.",
            "Kargolama inanılmaz hızlıydı. Siparişimin ertesi günü elimdeydi.",
            "Biraz pratik yapmak gerekiyor tabi ama tekerlek sistemi çok sağlam. Güvenle kullanıyoruz.",
            "Kızım o kadar çok sevdi ki yatağa bile bunlarla girmek istiyor. Kesinlikle her çocuğun hayali.",
            "Malzeme kalitesi muazzam. Daha önce başka bir marka denemiştik, hemen bozulmuştu. Bu çok dayanıklı.",
            "Çocuklar için inanılmaz bir eğlence aracı. Herkese tavsiye ediyorum.",
            "Ağırlığı bir tık fazla gibi ama çocuklar alıştığında sorun olmuyor. Işıkları şahane.",
            "Kalıpları tam, çocuğunuzun tam ayak numarasını alabilirsiniz.",
            "Düğmesine basıp tekerleği içeri sokmak çok zevkli. Çok havalı duruyor.",
            "Görselliği ve ışıkları çok dikkat çekiyor. Sokakta gören herkes nereden aldığımızı soruyor.",
            "Oğlumun paten kullanmayı öğrenmesi için harika bir başlangıç oldu. Çok dengeli.",
            "İki çocuğuma da birer tane aldım. İkisi de çok memnun. Kesinlikle pişman olmazsınız.",
            "Paten kısmı biraz ses yapıyor ama rahatsız edici boyutta değil. Kullanımı kolay.",
            "Satıcı sorularıma anında dönüş yaptı, çok ilgililer. Ürün de efsane.",
            "Hızlı kargo, özenli paketleme, kaliteli ürün. Başka söze gerek yok.",
            "Tabanı kalın olduğu için ayakları da üşütmüyor. Malzemesi tok duruyor.",
            "Ayakkabı kısmı biraz sert ilk giyildiğinde ama sonradan yumuşadı. Gayet rahat.",
            "Mekanizması hiç takılmıyor, yağ gibi kayıyor. Tekerlekler çok kaliteli.",
            "Çocukları dışarı çıkarmak için harika bir bahane oldu, sürekli kaymak istiyorlar.",
            "Bence piyasadaki en kaliteli ışıklı paten ayakkabısı. Diğer uyduruk ürünlerle kıyaslamayın.",
            "Güvenlik önlemi olarak sadece düz ve pürüzsüz zeminlerde kullanılmasını tavsiye ederim.",
            "Kızımın yüzündeki o mutluluk her şeye değer. Muhteşem bir ürün.",
            "Tasarımı çok modern, ayakkabı olarak da günlük kullanımda sırıtmaz.",
            "Şarjlı olması ışıklar için çok iyi. Pille uğraşmıyorsunuz. Işık renkleri çok güzel.",
            "Oğlum ayaklarında bunlarla resmen uçuyor. Dengesi çok iyi ayarlanmış.",
            "Beklediğimden biraz büyük geldi ama seneye de giyer diye değiştirmedim. Çok sağlam bir şey.",
            "Her adımda ışıkların yanması çocukların çok hoşuna gidiyor. Görsel şölen resmen.",
            "Satıcı o kadar ilgili ki, bir sorun yaşadığımızda anında çözdüler. Teşekkürler.",
            "İçindeki gizli düğme sayesinde okulda falan öğretmenleri kızmıyor, normal ayakkabı gibi.",
            "Ürünün kendi kutusu çok şıktı, hediye etmek için çok uygun bir yapısı var.",
            "Kızım pembe rengine bayıldı. Paten kısmı da çok güvenli görünüyor.",
            "Tekerlek kalitesi cidden beklentimin üstünde çıktı. Pürüzsüz kayıyor.",
            "Denediğimiz diğer markalara göre oldukça hafif, çocuk yorulmadan sürüyor.",
            "Fiyatı biraz yüksek gelebilir ama inanın kalitesiyle bunu tamamen hak ediyor.",
            "İçi pofuduk, ayakları hiç yara yapmadı. Benim için en önemlisi buydu.",
            "Küçük tekerlekli modellerden çok daha iyi, denge sağlamak kolay.",
            "1 aydır kullanıyoruz her gün, ne mekanizmada ne tekerlekte en ufak bir sorun yok.",
            "Kargo süreci çok başarılıydı, her adımda mesajla bilgilendirildik.",
            "Çocuklar resmen ayakkabıdan inmek istemiyor, evin içinde bile giyiyorlar.",
            "Bağcık sistemi çok pratik, çocuk kendi kendine kolayca bağlayıp çözebiliyor.",
            "Işıkları gündüz bile çok parlak belli oluyor. Akşamları ise inanılmaz.",
            "Tasarımı biraz hantal gibi duruyor ama ayakta çok şık duruyor bence.",
            "Oğlumun ayak terleme sorunu var ama bu ayakkabıda hiç terlemedi, hava alıyor sanırım.",
            "Tekerleklerin gizlenme mekanizması çok sağlam tasarlanmış, çıt diye oturuyor.",
            "Kesinlikle 1 numara büyük alınmalı kalıpları bir tık dar. Onun haricinde ürün 10 numara.",
            "Sınıftaki bütün arkadaşlarında var diye aldık, cidden çok eğlenceliler.",
            "Rengarenk yanan ışıkları var, favori renginde sabitleyebiliyorsunuz.",
            "Güvenle alabilirsiniz, tamamen görsellerdekiyle birebir aynı ve orijinal.",
            "Düşme riski çok az çünkü ağırlık merkezi yere çok yakın. Çocuklar hemen alışıyor.",
            "Tekerlekleri çıkardığınızda çok güzel bir spor ayakkabıya dönüşüyor. Çok fonksiyonel.",
            "Çocuğun denge duygusunu ve reflekslerini geliştirmesi için süper bir aktivite aracı.",
            "Kızım için harika bir sürpriz oldu, gözlerine inanamadı. Mükemmel.",
            "Bir tık ağır ama ışıklı patenli bir ayakkabı için gayet normal. Oğlum hiç şikayet etmiyor.",
            "Tekerleği açıp kapama düğmesi çok sert değil, çocuk kendi yapabiliyor.",
            "Kalitesi, duruşu, tekerleklerin akıcılığı... Her şeyiyle kusursuz bir ürün.",
            "Yeğenim o kadar mutlu oldu ki videoya çekip yolladı. Satıcı firmaya sonsuz teşekkürler.",
            "Uzun araştırmalarım sonucu bu modeli seçtim ve çok doğru bir karar vermişim.",
            "Ayağı sıkıca kavrıyor, bilek burkulmalarına karşı destekli yapılmış.",
            "Asfaltta kullanmak için ideal. Toprak yolda tekerlekler biraz zorlanabilir.",
            "Hem yürüyüş ayakkabısı hem paten. İki ürün bir arada. Çok pratik.",
            "Sipariş verdikten birkaç saat sonra kargoya verildi. Ertesi sabah bizdeydi.",
            "Paten öğrenmeye yeni başlayan çocuklar için mükemmel bir geçiş ürünü.",
            "Işıkları çok dikkat çekici. Gece yürüyüşlerinde harika görünüyor.",
            "Ürünün taban kalitesi çok yüksek, tekerlek açıkken bile çok dengeli.",
            "Çocukları ekrandan uzaklaştırmak için harika bir yöntem. Bütün gün paten kayıyorlar.",
            "Rengi fotoğraflardan çok daha parlak ve güzel. Çok havalı.",
            "Tekerlekler esnek silikon gibi bir malzemeden, plastik gibi değil. Çok sessiz.",
            "İhtiyaç olursa tekerlekleri tamamen sökülebiliyormuş, bu özelliği de çok beğendim.",
            "Ayakkabıyı her gören çok beğeniyor. Kalitesi uzaktan bile belli.",
            "Fiyat olarak çok uygun buldum bu kaliteye göre. Kesinlikle değer.",
            "Hızlı teslimat, özenli kargo. Satıcı her soruya sabırla cevap veriyor.",
            "Çocuğumun motor becerilerinin gelişmesine büyük katkı sağladı.",
            "Okulda herkes neresinden aldığımızı sormuş. Bizimki de çok havalı hissediyor.",
            "Tekerlekli mekanizması çok pürüzsüz çalışıyor, takılma vs asla yapmıyor.",
            "Çocuğunuzun ayak numarası buçukluysa bir üst bedeni tercih edebilirsiniz.",
            "Ön kısmı korumalı yapılmış, çarpma durumunda parmakları koruyor.",
            "Tekerlekli ayakkabılar içinde en iyi marka bence. Güven veriyor.",
            "Ürünü alır almaz evde test sürüşü yapıldı. Her şey sorunsuz.",
            "Oğlum için aldık ama kızıma da en kısa zamanda sipariş vereceğim.",
            "Tekerleklerini içeri kapatıp normal yürümesi çok iyi, hiç ses yapmıyor.",
            "Ürün kutusundan çok özenli çıktı. Emeği geçenlere teşekkürler."
        ];

        shuffle($names);
        shuffle($comments);

        $nameIndex = 0;
        $commentIndex = 0;

        foreach ($products as $product) {
            // Sadece hiç yorumu olmayan ürünlere eklensin ki link defalarca tıklandığında aynı ürüne yüzlerce yorum yığılmasın.
            if ($product->reviews()->count() > 0) {
                continue;
            }

            for ($i = 0; $i < 5; $i++) {
                
                // Rating mostly 5, some 4. e.g. 80% chance of 5 stars, 20% of 4 stars.
                $rating = rand(1, 100) > 20 ? 5 : 4;

                // Random date within the last 6 months
                $createdAt = Carbon::now()->subDays(rand(1, 180))->subHours(rand(1, 24));

                Review::create([
                    'product_id' => $product->id,
                    'user_id' => null,
                    'name' => $names[$nameIndex % count($names)],
                    'email' => 'customer' . $nameIndex . '@example.com',
                    'rating' => $rating,
                    'comment' => $comments[$commentIndex % count($comments)],
                    'status' => true,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $nameIndex++;
                $commentIndex++;
            }
        }

        $this->command->info('Successfully generated 5 realistic reviews for each of the ' . $products->count() . ' products.');
    }
}
