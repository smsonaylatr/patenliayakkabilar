<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sms_consent' => 'boolean',
        'email_consent' => 'boolean',
        'abandoned_sms_sent_at' => 'datetime',
        'reminder_mail_sent_at' => 'datetime',
        'coupon_mail_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function setGuestPhoneAttribute($value): void
    {
        $this->attributes['guest_phone'] = $value !== null ? substr(trim((string) $value), 0, 30) : null;
    }

    public function setGuestNameAttribute($value): void
    {
        $this->attributes['guest_name'] = $value !== null ? mb_substr(trim((string) $value), 0, 100) : null;
    }

    public function setGuestEmailAttribute($value): void
    {
        $this->attributes['guest_email'] = $value !== null ? mb_substr(trim((string) $value), 0, 100) : null;
    }
}
