<?php

namespace Database\Seeders;

use App\Models\Backlink;
use Illuminate\Database\Seeder;

class BacklinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backlinks = [
            // ==========================================
            // 1. SOSYAL & PROFİL / OTORİTE SİNYALLERİ
            // ==========================================
            [
                'title' => 'Google İşletme Profili (Google Maps)',
                'domain' => 'google.com/business',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'social_profile',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 100,
                'notes' => 'Harita konumu, çalışma saatleri, ürün kataloğu ve web sitesi URL eklenmeli.',
            ],
            [
                'title' => 'Pinterest Business Hesabı & Panoları',
                'domain' => 'pinterest.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'social_profile',
                'anchor_text' => 'Patenli Ayakkabı Modelleri',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 94,
                'notes' => 'Web sitesini doğrula (meta tag ile). Tüm ürün görsellerini panolara pinle ve ürün URL linklerini tanımla.',
            ],
            [
                'title' => 'Medium Yayın & Makale Profili',
                'domain' => 'medium.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'social_profile',
                'anchor_text' => 'Patenli Ayakkabılar Türkiye',
                'link_type' => 'nofollow',
                'status' => 'pending',
                'domain_authority' => 95,
                'notes' => 'Çocuk aktiviteleri ve patenli ayakkabı seçimi üzerine 2 adet otoriter blog makalesi yayınla.',
            ],
            [
                'title' => 'YouTube Kanal Profili & Video Açıklamaları',
                'domain' => 'youtube.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'social_profile',
                'anchor_text' => 'https://patenliayakkabilar.com',
                'link_type' => 'nofollow',
                'status' => 'pending',
                'domain_authority' => 99,
                'notes' => 'Kanal "Hakkında" kısmına web sitesi linki ve yayınlanan tekerlekli ayakkabı kullanım videolarına ürün linki ekle.',
            ],
            [
                'title' => 'LinkedIn Şirket Sayfası',
                'domain' => 'linkedin.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'social_profile',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'nofollow',
                'status' => 'pending',
                'domain_authority' => 98,
                'notes' => 'Kurumsal şirket sayfası oluşturup web sitesi butonunu bağla.',
            ],

            // ==========================================
            // 2. TÜRKİYE FİRMA & E-TİCARET DİZİNLERİ
            // ==========================================
            [
                'title' => 'ETBİS (Elektronik Ticaret Bilgi Sistemi)',
                'domain' => 'etbis.eticaret.gov.tr',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'directory',
                'anchor_text' => 'Patenli Ayakkabılar E-Ticaret',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 85,
                'notes' => 'Ticaret Bakanlığı ETBİS kaydı tamamlanıp karekod site footerına entegre edilmeli.',
            ],
            [
                'title' => 'YellowPages Türkiye Firma Rehberi',
                'domain' => 'yellowpages.com.tr',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'directory',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 58,
                'notes' => 'Ayakkabı Mağazası ve E-Ticaret kategorisine ücretsiz firma kaydı yap.',
            ],
            [
                'title' => 'Bulurum.com Firma Dizini',
                'domain' => 'bulurum.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'directory',
                'anchor_text' => 'Patenli Ayakkabı Satışı',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 52,
                'notes' => 'Firma profili açılıp adres ve web sitesi tanımlanacak.',
            ],
            [
                'title' => 'Firmasayfasi.com E-Ticaret Dizini',
                'domain' => 'firmasayfasi.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'directory',
                'anchor_text' => 'tekerlekli ayakkabı',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 45,
                'notes' => 'Ücretsiz firma profili ve ürün açıklaması ekle.',
            ],
            [
                'title' => 'Foursquare / Swarm Mekan & İşletme Kaydı',
                'domain' => 'foursquare.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'directory',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 92,
                'notes' => 'Mağaza / İşletme profili oluşturularak web sitesi linki girilecek.',
            ],

            // ==========================================
            // 3. ANNE-ÇOCUK & EBEVEYN PORTALLARI (OUTREACH)
            // ==========================================
            [
                'title' => 'Anneysen.com Blog & Forum Tohumlama',
                'domain' => 'anneysen.com',
                'target_url' => 'https://patenliayakkabilar.com/blog/patenli-ayakkabi-alirken-dikkat-edilmesi-gerekenler',
                'backlink_url' => null,
                'category' => 'parenting_blog',
                'anchor_text' => 'patenli ayakkabı beden rehberi',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 48,
                'notes' => 'Ebeveynlerin çocuk hediyesi ve spor ayakkabı seçim sorularına uzman rehber linki ile yanıt ver.',
            ],
            [
                'title' => 'Kadinlarkulubu Ebeveyn & Çocuk Bölümü',
                'domain' => 'kadinlarkulubu.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'parenting_blog',
                'anchor_text' => 'ışıklı patenli ayakkabı',
                'link_type' => 'ugc',
                'status' => 'pending',
                'domain_authority' => 64,
                'notes' => 'Çocuk doğum günü hediye tavsiyeleri başlıklarında deneyim paylaşımı tohumlaması.',
            ],
            [
                'title' => 'Hassas Anne & Ebeveyn Blogları Outreach',
                'domain' => 'hassasanne.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'parenting_blog',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 42,
                'contact_name' => 'Editör Ekibi',
                'notes' => 'Çocukları ekrandan uzaklaştırıp açık havada harekete geçiren oyuncaklar konseptli konuk makale teklifi.',
            ],
            [
                'title' => 'CocukluDunya Portalı',
                'domain' => 'cocukludunya.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'parenting_blog',
                'anchor_text' => 'çocuk tekerlekli ayakkabı',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 38,
                'notes' => 'Çocuk etkinlik ve spor araçları listelemesinde ürün inceleme sponsorluğu.',
            ],

            // ==========================================
            // 4. SPOR, KAYKAY & YAŞAM (LIFESTYLE) SİTELERİ
            // ==========================================
            [
                'title' => 'Paten & Kaykay Topluluk Blogları',
                'domain' => 'patenkulubu.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'sports_lifestyle',
                'anchor_text' => '2 tekerlekli patenli ayakkabı',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 35,
                'notes' => 'Yeni başlayanlar için tekerlekli ayakkabı dengesi rehberimize backlink alma.',
            ],
            [
                'title' => 'SporveGelisim Lifestyle Portalı',
                'domain' => 'sporvegelisim.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'sports_lifestyle',
                'anchor_text' => 'çocuk spor ayakkabıları',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 32,
                'notes' => 'Çocuklarda denge ve motor kas gelişimi makalesine link yerleşimi.',
            ],

            // ==========================================
            // 5. FORUM & TOPLULUK TOHUMLAMA (UGC LINKS)
            // ==========================================
            [
                'title' => 'DonanımHaber Sıcak Fırsatlar & Alışveriş Tavsiyesi',
                'domain' => 'forum.donanimhaber.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'forum_community',
                'anchor_text' => 'Patenli Ayakkabılar indirimli',
                'link_type' => 'ugc',
                'status' => 'pending',
                'domain_authority' => 78,
                'notes' => 'Yeni sezon veya açılış indirim kuponunu indirim & fırsat başlığı altında paylaş.',
            ],
            [
                'title' => 'KizlarSoruyor Hediye Öneri Başlıkları',
                'domain' => 'kizlarsoruyor.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'forum_community',
                'anchor_text' => 'https://patenliayakkabilar.com',
                'link_type' => 'ugc',
                'status' => 'pending',
                'domain_authority' => 72,
                'notes' => '"Kardeşime / yeğenime ne hediye alabilirim?" sorularında ürün referansı tohumlaması.',
            ],
            [
                'title' => 'Ekşi Sözlük Başlık Tohumlama',
                'domain' => 'eksisozluk.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'forum_community',
                'anchor_text' => 'patenli ayakkabı',
                'link_type' => 'ugc',
                'status' => 'pending',
                'domain_authority' => 84,
                'notes' => '"Patenli ayakkabı" veya "tekerlekli ayakkabı" başlığında çocukluk anısı ve güncel marka deneyimi yorumu.',
            ],

            // ==========================================
            // 6. DİJİTAL PR & BASIN BÜLTENİ DAĞITIMI
            // ==========================================
            [
                'title' => 'BThaber E-Ticaret ve Girişim Haberleri',
                'domain' => 'bthaber.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'digital_pr',
                'anchor_text' => 'PatenliAyakkabilar.com',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 54,
                'notes' => '"Çocukları dijital ekranlardan açık havaya yönlendiren yeni nesil e-ticaret markası" basın bülteni gönderimi.',
            ],
            [
                'title' => 'Webrazzi / Webrazzi Girişim Portalı',
                'domain' => 'webrazzi.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'digital_pr',
                'anchor_text' => 'Patenli Ayakkabılar',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 76,
                'notes' => 'Niş dikey e-ticaret girişimi profili ve haber bülteni.',
            ],

            // ==========================================
            // 7. HEDİYE & ALIŞVERİŞ REHBERİ (GIFT GUIDES)
            // ==========================================
            [
                'title' => 'HediyeSepeti & Tavsiye Blogları',
                'domain' => 'hediyefikirleri.org',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'gift_guide',
                'anchor_text' => 'çocuklar için patenli ayakkabı modelleri',
                'link_type' => 'dofollow',
                'status' => 'pending',
                'domain_authority' => 40,
                'notes' => '"7-14 Yaş Çocuklar İçin En Popüler 10 Doğum Günü Hediyesi" listesine link yerleşimi.',
            ],
            [
                'title' => 'Onedio Yaşam & Alışveriş Listeleri',
                'domain' => 'onedio.com',
                'target_url' => 'https://patenliayakkabilar.com',
                'backlink_url' => null,
                'category' => 'gift_guide',
                'anchor_text' => 'patenli ayakkabı modelleri',
                'link_type' => 'nofollow',
                'status' => 'pending',
                'domain_authority' => 86,
                'notes' => 'Viral liste içeriklerinde "Yaz aylarında çocukların bayılacağı açık hava ürünleri" maddesine ekleme.',
            ],
        ];

        foreach ($backlinks as $item) {
            Backlink::updateOrCreate(
                ['domain' => $item['domain'], 'title' => $item['title']],
                $item
            );
        }
    }
}
