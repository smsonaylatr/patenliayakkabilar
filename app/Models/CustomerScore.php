<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerScore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'lifetime_value' => 'decimal:2',
        'avg_order_value' => 'decimal:2',
        'predicted_churn_probability' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOverallScoreAttribute(): int
    {
        return (int) round(
            ($this->activity_score * 0.2) +
            ($this->purchase_score * 0.3) +
            ($this->loyalty_score * 0.25) +
            ($this->engagement_score * 0.25)
        );
    }

    public function getTierAttribute(): string
    {
        $score = $this->overall_score;
        return match (true) {
            $score >= 80 => 'VIP',
            $score >= 60 => 'Değerli',
            $score >= 40 => 'Normal',
            $score >= 20 => 'Düşük',
            default => 'Yeni',
        };
    }

    public function getTierColorAttribute(): string
    {
        return match ($this->tier) {
            'VIP' => 'success',
            'Değerli' => 'primary',
            'Normal' => 'info',
            'Düşük' => 'warning',
            'Yeni' => 'gray',
        };
    }
}
