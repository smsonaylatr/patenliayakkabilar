<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_winner' => 'boolean',
        'kvkk_consent' => 'boolean',
        'sms_consent' => 'boolean',
    ];

    /**
     * Benzersiz Kura Numarası Üret
     */
    public static function generateTicketCode(): string
    {
        do {
            $code = 'PTN-' . rand(10000, 99999);
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }
}
