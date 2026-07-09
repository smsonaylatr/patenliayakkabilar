<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerCampaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'products_sent' => 'array',
        'video_urls' => 'array',
        'offered_amount' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'roi' => 'decimal:2',
        'total_views' => 'integer',
        'total_clicks' => 'integer',
        'total_sales' => 'integer',
        'expected_videos' => 'integer',
        'delivered_videos' => 'integer',
        'sent_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
