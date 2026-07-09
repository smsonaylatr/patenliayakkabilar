<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerOutreachLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ai_generated' => 'boolean',
        'response_received' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
