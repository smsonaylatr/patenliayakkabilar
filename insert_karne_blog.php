<?php
/**
 * Karne Hediyesi blog yazısını doğrudan DB'ye ekleyen standalone script.
 * Kullanım: php insert_karne_blog.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BlogPost;

$post = BlogPost::updateOrCreate(
    ['slug' => 'karne-hediyesi-olarak-ne-alinir-cocuklar-icin-en-dogru-hediye-fikirleri'],
    [
        'title' => 'Karne Hediyesi Olarak Ne Alınır? Çocuklar İçin En Doğru Hediye Fikirleri',
        'excerpt' => 'Karne dönemi çocuğunuza en anlamlı hediyeyi vermek istiyorsanız, doğru seçim rehberi burada! Yaşa uygun, eğlenceli ve aktif yaşamı destekleyen karne hediyesi fikirleri arasında patenli ayakkabılar neden bir numara? Detaylı rehberimizi okuyun.',
        'author_name' => 'Patenli Ayakkabılar Editör Ekibi',
        'published_at' => now(),
        'status' => true,
        'is_indexable' => true,
        'meta_title' => 'Karne Hediyesi Olarak Ne Alınır? Çocuklar İçin En İyi Hediye Fikirleri',
        'meta_description' => 'Karne hediyesi olarak ne alınır? Çocuklar için en doğru hediye fikirleri, yaşa uygun seçim rehberi ve patenli ayakkabı ile aktif hediye önerileri.',
        'image_alt' => 'Karne Hediyesi Olarak Ne Alınır - Çocuklar İçin Hediye Fikirleri',
        'og_image' => null,
        'aio_summary' => 'Karne dönemi çocuklar için önemli bir motivasyon kaynağıdır. En doğru karne hediyesi seçimi yaşa uygun, uzun süreli kullanılabilen ve çocuğun gelişimini destekleyen ürünlerle yapılır. Patenli ayakkabılar; hareket, eğlence ve motor beceri gelişimini aynı anda sunan ideal bir karne hediyesidir.',
        'aio_target_keywords' => [
            'karne hediyesi',
            'karne hediyesi olarak ne alınır',
            'çocuklar için karne hediyesi',
            'en iyi karne hediyesi fikirleri',
            'karne hediyesi oyuncak',
            'çocuklara karne hediyesi',
            'patenli ayakkabı karne hediyesi',
            'tekerlekli ayakkabı hediye',
        ],
        'faq_schema' => [
            [
                'question' => 'Karne hediyesi olarak ne alınır?',
                'answer' => 'Karne hediyesi olarak çocuğun yaşına ve ilgi alanına uygun, uzun süreli kullanılabilen ürünler tercih edilmelidir. Patenli ayakkabılar, spor ekipmanları, eğitici oyunlar ve yaratıcı setler en popüler karne hediyesi seçenekleri arasındadır.',
            ],
            [
                'question' => 'Karne hediyesi seçerken nelere dikkat edilmeli?',
                'answer' => 'Karne hediyesi seçerken çocuğun yaş grubuna uygunluk, güvenlik standartları, uzun vadeli kullanım potansiyeli ve çocuğun aktif katılımını destekleyip desteklemediği değerlendirilmelidir.',
            ],
            [
                'question' => 'Patenli ayakkabı karne hediyesi olarak uygun mu?',
                'answer' => 'Evet, patenli ayakkabılar hem eğlence hem spor hem de motor beceri gelişimi sunduğu için en popüler ve işlevsel karne hediyesi seçeneklerinden biridir. Çocuklar ekran dışında aktif vakit geçirir.',
            ],
            [
                'question' => 'Kaç yaşındaki çocuklara patenli ayakkabı alınabilir?',
                'answer' => 'Patenli ayakkabılar genellikle 5 yaş ve üzeri çocuklar için uygundur. Denge becerisi gelişmiş çocuklarda 4 yaşından itibaren de güvenle kullanılabilir.',
            ],
            [
                'question' => 'En iyi karne hediyesi fikirleri nelerdir?',
                'answer' => 'Patenli ayakkabılar, scooter, bisiklet, puzzle ve yapı setleri, eğitici oyuncaklar, sanat ve aktivite setleri en iyi karne hediyesi fikirleri arasında yer alır.',
            ],
        ],
        'content' => file_get_contents(__DIR__ . '/database/seeders/karne_blog_content.html'),
    ]
);

echo "Blog yazisi basariyla eklendi! ID: {$post->id} | Slug: {$post->slug}\n";
