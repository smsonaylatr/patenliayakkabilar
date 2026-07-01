<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerSegment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($segment) {
            if (empty($segment->slug)) {
                $segment->slug = Str::slug($segment->name);
            }
        });
    }

    public function customers()
    {
        return $this->belongsToMany(User::class, 'customer_segment_user')
            ->withPivot('added_at');
    }
}
