<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'domain_authority' => 'integer',
    ];

    public const CATEGORIES = [
        'directory' => 'Firma / Sektörel Dizin',
        'social_profile' => 'Sosyal Medya / İçerik Profili',
        'parenting_blog' => 'Anne-Çocuk & Ebeveyn Blogu',
        'sports_lifestyle' => 'Spor, Kaykay & Yaşam Blogu',
        'forum_community' => 'Forum & Topluluk Tohumlama',
        'digital_pr' => 'Basın Bülteni & Dijital PR',
        'gift_guide' => 'Hediye & Alışveriş Rehberi',
        'review' => 'Ürün İnceleme & Karşılaştırma',
    ];

    public const STATUSES = [
        'pending' => 'Beklemede / Hedef Liste',
        'contacted' => 'İletişime Geçildi',
        'published' => 'Yayınlandı (Kontrol Edilecek)',
        'active_verified' => 'Aktif & Doğrulandı (Canlı)',
        'rejected' => 'Reddedildi / İptal',
    ];

    public const LINK_TYPES = [
        'dofollow' => 'Dofollow (Güç Aktarır)',
        'nofollow' => 'Nofollow',
        'ugc' => 'UGC (Kullanıcı İçeriği)',
        'sponsored' => 'Sponsored (Sponsorlu)',
    ];
}
