<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }
}
