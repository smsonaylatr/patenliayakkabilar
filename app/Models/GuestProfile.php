<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visit_count' => 'integer',
        'total_page_views' => 'integer',
        'total_time_spent_seconds' => 'integer',
        'first_seen_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activeVisitors(): HasMany
    {
        return $this->hasMany(ActiveVisitor::class, 'guest_profile_id');
    }

    /**
     * Yıldız sayısı: 1. geliş 1, 2. geliş 2, 3. geliş ve sonrası 3 yıldız
     */
    public function getStarsCountAttribute(): int
    {
        $count = (int) ($this->visit_count ?: 1);
        if ($count <= 1) {
            return 1;
        }
        if ($count === 2) {
            return 2;
        }
        return 3;
    }

    /**
     * Yıldız emoji dizesi
     */
    public function getStarsStringAttribute(): string
    {
        return match ($this->stars_count) {
            1 => '⭐',
            2 => '⭐⭐',
            default => '⭐⭐⭐',
        };
    }

    /**
     * Yıldız HTML rozeti
     */
    public function getStarsHtmlAttribute(): string
    {
        $count = (int) ($this->visit_count ?: 1);
        $title = $count . '. Gelişi';
        $stars = $this->stars_string;

        return '<span class="guest-stars" title="' . e($title) . '" style="color:#fbbf24;font-size:12.5px;letter-spacing:1px;display:inline-flex;align-items:center;vertical-align:middle;filter:drop-shadow(0 0 4px rgba(245,158,11,0.45));">' . $stars . '</span>';
    }

    /**
     * Kısa misafir ID'si (Örn: MUJORC)
     */
    public static function generateGuestId(string $token): string
    {
        $clean = strtoupper(substr(str_replace(['pa_vt_', '-', '_'], '', $token), 0, 6));
        return !empty($clean) ? $clean : strtoupper(\Illuminate\Support\Str::random(6));
    }
}
