<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Influencer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'subscriber_count' => 'integer',
        'avg_views' => 'integer',
        'engagement_rate' => 'decimal:2',
        'fit_score' => 'integer',
        'commission_rate' => 'decimal:2',
        'total_videos' => 'integer',
        'total_revenue' => 'decimal:2',
        'child_age' => 'integer',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(InfluencerCampaign::class);
    }

    public function outreachLogs(): HasMany
    {
        return $this->hasMany(InfluencerOutreachLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeHighFit($query, int $minScore = 70)
    {
        return $query->where('fit_score', '>=', $minScore);
    }

    public function getFormattedSubscribersAttribute(): string
    {
        if (!$this->subscriber_count) return 'Bilinmiyor';
        if ($this->subscriber_count >= 1000000) {
            return number_format($this->subscriber_count / 1000000, 1) . 'M';
        }
        if ($this->subscriber_count >= 1000) {
            return number_format($this->subscriber_count / 1000, 1) . 'K';
        }
        return (string) $this->subscriber_count;
    }

    public function getTierLabelAttribute(): string
    {
        return match($this->tier) {
            'nano' => 'Nano (1K-5K)',
            'micro' => 'Mikro (5K-50K)',
            'mid' => 'Orta (50K-500K)',
            'macro' => 'Makro (500K-1M)',
            'mega' => 'Mega (1M+)',
            default => $this->tier,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'discovered' => 'Keşfedildi',
            'contacted' => 'İletişime Geçildi',
            'negotiating' => 'Müzakere',
            'agreed' => 'Anlaşıldı',
            'active' => 'Aktif',
            'paused' => 'Duraklatıldı',
            'rejected' => 'Reddedildi',
            default => $this->status,
        };
    }
}
