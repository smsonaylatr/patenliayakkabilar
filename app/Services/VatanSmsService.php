<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class VatanSmsService
{
    protected string $apiUrl = 'https://api.vatansms.net/api/v1/1toN';

    /**
     * @param array|string $phones Telefon numarası veya numaralar dizisi
     * @param string $message Gönderilecek mesaj metni
     * @param string $messageType 'normal' veya 'turkce' (Türkçe karakter destekli)
     * @param string $contentType 'bilgi' (Bilgilendirme) veya 'ticari' (Ticari, kampanya vb.)
     * @return bool Başarılı ise true döner
     */
    public function send($phones, string $message, string $messageType = 'turkce', string $contentType = 'bilgi'): bool
    {
        try {
            $isActive = filter_var(Setting::where('key', 'vatansms_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
            if (!$isActive) {
                return false;
            }

            $apiId = Setting::where('key', 'vatansms_api_id')->value('value');
            $apiKey = Setting::where('key', 'vatansms_api_key')->value('value');
            $sender = Setting::where('key', 'vatansms_sender')->value('value');

            if (empty($apiId) || empty($apiKey) || empty($sender)) {
                Log::warning('VatanSMS ayarları eksik. Lütfen admin panelinden ayarları yapılandırın.');
                return false;
            }

            if (!is_array($phones)) {
                $phones = [$phones];
            }

            // Numaraları 10 haneli (başında 0 olmadan) olacak şekilde temizle
            $phones = array_map(function ($phone) {
                // Sadece rakamları bırak
                $phone = preg_replace('/[^0-9]/', '', (string)$phone);
                
                // Başında 90 varsa ve 12 haneliyse, 90'ı sil
                if (strlen($phone) === 12 && str_starts_with($phone, '90')) {
                    $phone = substr($phone, 2);
                }
                
                // Başında 0 varsa ve 11 haneliyse, 0'ı sil
                if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
                    $phone = substr($phone, 1);
                }
                
                return $phone;
            }, $phones);
            
            // Boş veya 10 haneli olmayanları filtrele ve indisleri sıfırla
            $phones = array_values(array_filter($phones, fn($phone) => strlen($phone) === 10));
            
            if (empty($phones)) {
                Log::warning('VatanSMS Hata: Geçerli bir telefon numarası bulunamadı.');
                return false;
            }

            $payload = [
                'api_id' => $apiId,
                'api_key' => $apiKey,
                'sender' => $sender,
                'message_type' => $messageType,
                'message' => $message,
                'message_content_type' => $contentType,
                'phones' => $phones,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(10)->post($this->apiUrl, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('VatanSMS API Hatası: ' . $response->body());
            return false;

        } catch (\Throwable $th) {
            Log::error('VatanSMS İstek Hatası: ' . $th->getMessage());
            return false;
        }
    }
}
