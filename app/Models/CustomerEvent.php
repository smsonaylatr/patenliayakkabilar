<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerEvent extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'event_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
