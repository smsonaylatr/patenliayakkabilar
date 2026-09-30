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

            $fromName = (!empty($settings["mail_{$type}_from_name"]) && trim($settings["mail_{$type}_from_name"]) !== '')
                ? trim($settings["mail_{$type}_from_name"])
                : (!empty($settings['smtp_from_name']) ? trim($settings['smtp_from_name']) : $defaultName);

            $fromAddress = (!empty($settings["mail_{$type}_from_address"]) && trim($settings["mail_{$type}_from_address"]) !== '')
                ? trim($settings["mail_{$type}_from_address"])
                : (!empty($settings['smtp_from_address']) ? trim($settings['smtp_from_address']) : $defaultAddress);

            return [$fromAddress, $fromName];
        } catch (\Throwable) {
            return [$defaultAddress, $defaultName];
        }
    }

    public static function getMailSubject(string $type, string $defaultSubject, array $replacements = []): string
    {
        try {
            $customSubject = static::where('key', "mail_{$type}_subject")->value('value');
            $subject = (!empty($customSubject) && trim($customSubject) !== '') ? trim($customSubject) : $defaultSubject;

            foreach ($replacements as $placeholder => $value) {
                $subject = str_ireplace($placeholder, (string)$value, $subject);
            }

            return $subject;
        } catch (\Throwable) {
            $subject = $defaultSubject;
            foreach ($replacements as $placeholder => $value) {
                $subject = str_ireplace($placeholder, (string)$value, $subject);
            }
            return $subject;
        }
    }
}
