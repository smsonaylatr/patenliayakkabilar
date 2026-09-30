<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = static::where('key', $key)->value('value');
            return ($value !== null && $value !== '') ? $value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function getMailSender(string $type, string $defaultName = 'Patenli Ayakkabılar®', string $defaultAddress = 'siparis@patenliayakkabilar.com'): array
    {
        try {
            $settings = static::whereIn('key', [
                "mail_{$type}_from_name",
                "mail_{$type}_from_address",
                'smtp_from_name',
                'smtp_from_address'
            ])->pluck('value', 'key')->toArray();

            $fromName = !empty($settings["mail_{$type}_from_name"])
                ? $settings["mail_{$type}_from_name"]
                : (!empty($settings['smtp_from_name']) ? $settings['smtp_from_name'] : $defaultName);

            $fromAddress = !empty($settings["mail_{$type}_from_address"])
                ? $settings["mail_{$type}_from_address"]
                : (!empty($settings['smtp_from_address']) ? $settings['smtp_from_address'] : $defaultAddress);

            return [$fromAddress, $fromName];
        } catch (\Throwable) {
            return [$defaultAddress, $defaultName];
        }
    }
}
