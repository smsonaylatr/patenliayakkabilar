<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateReviewsForEmptyProducts extends Command
{
    protected $signature = 'reviews:generate-empty 
                            {--count=5 : Her ürüne eklenecek yorum sayısı} 
                            {--force : Zaten yorumu olan ürünlere de ekle}';

    protected $description = 'Yorumu bulunmayan ürünlere rastgele gerçekçi, içeriğe uygun ve övgü dolu 5 adet müşteri yorumu ekler';

    public function handle(): int
    {
        $count = (int) $this->option('count') ?: 5;
        $force = (bool) $this->option('force');

        $query = Product::query();
        if (!$force) {
            $query->doesntHave('reviews');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->info('Yorum eklenecek ürün bulunamadı. Tüm ürünlerin en az bir yorumu mevcut.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$products->count()} adet ürün için yorumlar oluşturuluyor...");

        $totalAdded = 0;

        foreach ($products as $product) {
            $added = self::generateReviewsForProduct($product, $count);
            $totalAdded += $added;
            $this->line("✓ [{$product->id}] {$product->name} -> {$added} yorum eklendi.");
        }

        $this->info("İşlem tamamlandı! Toplam {$totalAdded} adet onaylı müşteri yorumu oluşturuldu.");

        return self::SUCCESS;
    }

    /**
     * Belirtilen ürüne gerçekçi ve içeriğe uygun yorumlar üretir.
     */
    public static function generateReviewsForProduct(Product $product, int $count = 5): int
    {
        $namesPool = [
            'Osman S.', 'Elif K.', 'Burak Y.', 'Zeynep D.', 'Mert Ç.', 'Selin A.',
            'Ahmet Y.', 'Ayşe Ö.', 'Emre K.', 'Gamze A.', 'Kaan D.',
            'Büşra Ş.', 'Tolga Ö.', 'Derya K.', 'Murat G.', 'Sevgi T.',
            'Cemre E.', 'Onur A.', 'Ece P.', 'Sinan Ç.', 'Tuğba K.',
            'Yasin B.', 'Ebru Y.', 'Deniz Y.', 'Gökhan Ş.', 'Melis U.',
            'Serkan A.', 'Damla Ç.', 'Oğuzhan K.', 'Gizem T.', 'Fatih Y.',
            'İrem B.', 'Hakan A.', 'Ceren Ş.', 'Berk K.', 'Hande Ö.',
            'Ali Rıza T.', 'Yasemin G.', 'Ceyda B.', 'Tarkan M.', 'Aslıhan S.',
            'Mehmet Emin K.', 'Fatma Zehra A.', 'Kerem V.', 'Pelin S.', 'Cenk Ö.',
            'Dilara E.', 'Volkan S.', 'Berna A.', 'Barış G.', 'Nihan K.'
        ];

        // İsimleri karıştır
        shuffle($namesPool);

        $templates = self::getReviewTemplates($product);
        shuffle($templates);

        $selectedTemplates = array_slice($templates, 0, $count);
        $selectedNames = array_slice($namesPool, 0, $count);

        $added = 0;

        for ($i = 0; $i < count($selectedTemplates); $i++) {
            $name = $selectedNames[$i] ?? 'Müşteri';
            $comment = $selectedTemplates[$i];
            
            // %90 5 yıldız, %10 4 yıldız
            $rating = (rand(1, 10) <= 9) ? 5 : 4;
            
            // Son 2 ile 45 gün arasında rastgele bir tarih
            $daysAgo = rand(2, 45);
            $hoursAgo = rand(1, 23);
            $minutesAgo = rand(5, 55);
            $createdAt = Carbon::now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo);

            Review::create([
                'product_id' => $product->id,
                'user_id'    => null,
                'order_id'   => null,
                'name'       => $name,
                'email'      => null,
                'rating'     => $rating,
                'comment'    => $comment,
                'images'     => null,
                'status'     => 1, // Onaylı
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $added++;
        }

        return $added;
    }

    /**
     * Ürün özelliklerine ve kategorisine özel doğal müşteri yorum şablonları
     */
    private static function getReviewTemplates(Product $product): array
    {
        $gender = $product->gender;
        $childWord = match ($gender) {
            'kiz_cocuk'   => 'Kızıma',
            'erkek_cocuk' => 'Oğluma',
            default       => 'Çocuğuma',
        };

        $pronoun = match ($gender) {
            'kiz_cocuk'   => 'kızım',
            'erkek_cocuk' => 'oğlum',
            default       => 'çocuğumuz',
        };

        $templates = [
            // Hediye & Mutluluk
            "{$childWord} doğum günü hediyesi olarak aldık, paketi açtığı andaki sevinci paha biçilemezdi. Bütün gün ayağından çıkarmıyor, kesinlikle tavsiye ederim.",
            "{$childWord} aldım, ilk defa paten denemesine rağmen dengesi ve bas-çek mekanizması sayesinde yarım saatte çözdü. Harika bir ürün!",
            "Karnesi için hediye aldık, beklentimizin çok üzerinde çıktı. Malzeme kalitesi, dikişleri ve tekerlek mekanizması çok sağlam.",
            "Uzun zamandır istediği bir modeldi. Görsellerdekinden bile daha canlı ve kaliteli geldi. Teşekkürler Patenli Ayakkabılar!",
            "Yeğenime hediye aldım, bayıldı! Tekerlekleri çok rahat açılıp kapanıyor, normal ayakkabı olarak da çok şık duruyor.",

            // Mekanizma & Kalite
            "Bas-çek gizlenebilir tekerlek mekanizması gerçekten çok pratik. Parkta kayıp AVM'ye girerken tek tuşla kapatıyoruz, çok kullanışlı.",
            "Tekerlekleri son derece akıcı ve sessiz kayıyor. Kauçuk tabanı sayesinde normal yürürken de hiç kayma yapmıyor, çok güvenli.",
            "Mekanizması çok sağlam ve kaliteli malzemeden üretilmiş. Takılma yapmıyor, buton kilit sistemi güven veriyor.",
            "Hem spor ayakkabı konforunda hem de paten keyfi sunuyor. Çift fonksiyonlu olması büyük avantaj.",

            // Işık & Tasarım
            "Işıkları hareket ettikçe rengarenk ve çok canlı yanıyor. Akşamları yürüyüş yaparken herkes hayran kalıyor.",
            "Tasarımı çok havalı ve modern. Sokakta gören diğer çocuklar hemen nereden aldığımızı soruyor.",
            "Rengi ve dokusu harika. Işıklandırması çok enerjik ve kaliteli duruyor.",

            // Kalıp & Konfor
            "Kalıbı tam oldu, günlük giydiği spor ayakkabı numarasını tercih ettik birebir uydu. İçi oldukça rahat ve yumuşak.",
            "Ayağı çok iyi kavrıyor ve bilek desteği çok başarılı. Kayarken bileği burkulmuyor, gönül rahatlığıyla kullanıyoruz.",
            "Paten mekanizması olmasına rağmen şaşırtıcı derecede hafif ve rahat. Ayakta ağırlık yapmıyor.",

            // Kargo & Müşteri Memnuniyeti
            "Siparişi verdikten 1 gün sonra kargoya verildi ve ertesi gün elimize ulaştı. Paketleme çok özenliydi, çok memnun kaldık.",
            "Fiyatını son kuruşuna kadar hak eden kaliteli bir ürün. Hızlı kargo ve özenli paketleme için satıcıya teşekkür ederim.",
            "Ürünün yanında gelen kullanım talimatı ve bilgilendirme çok faydalı oldu. Kaliteli esnaflık, çok teşekkürler.",
        ];

        return $templates;
    }
}
