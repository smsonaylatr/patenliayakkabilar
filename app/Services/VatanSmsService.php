<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class VatanSmsService
{
    protected string $apiBaseUrl = 'https://api.toplusms.app/api/v1';
    public ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * WaMessage (TopluSMS) API üzerinden SMS gönderir.
     * 
     * @param array|string $phones Telefon numarası veya numaralar dizisi
     * @param string $message Gönderilecek mesaj metni
     * @param string $messageType 'normal' veya 'turkce' (Türkçe karakter destekli)
     * @param string $contentType 'bilgi' (Bilgilendirme) veya 'ticari' (Ticari, kampanya vb.)
     * @return bool Başarılı ise true döner
     */
    public function send($phones, string $message, string $messageType = 'turkce', string $contentType = 'bilgi'): bool
    {
        $this->lastError = null;
        try {
            $isActive = filter_var(Setting::where('key', 'vatansms_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
            if (!$isActive) {
                $this->lastError = 'SMS panelden pasif durumda.';
                return false;
            }

            $apiKey = trim((string) Setting::where('key', 'vatansms_api_key')->value('value'));
            $sender = trim((string) Setting::where('key', 'vatansms_sender')->value('value'));

            if (empty($apiKey) || empty($sender)) {
                $this->lastError = 'SMS API Key veya Gönderici Başlığı eksik.';
                Log::warning('WaMessage SMS ayarları eksik. Lütfen admin panelinden ayarları yapılandırın.');
                return false;
            }

            if (!is_array($phones)) {
                $phones = [$phones];
            }

            // Numaraları 905xxxxxxxxx formatına çevir (WaMessage ülke kodu ister)
            $phones = array_map(function ($phone) {
                // Sadece rakamları bırak
                $phone = preg_replace('/[^0-9]/', '', (string)$phone);
                
                // 10 haneli ise başına 90 ekle (5xxxxxxxxx)
                if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
                    $phone = '90' . $phone;
                }
                
                // 11 haneli ve 0 ile başlıyorsa, 0'ı 90 ile değiştir (05xxxxxxxxx)
                if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
                    $phone = '90' . substr($phone, 1);
                }
                
                return $phone;
            }, $phones);
            
            // Boş veya 12 haneli olmayanları filtrele
            $phones = array_values(array_filter($phones, fn($phone) => strlen($phone) === 12 && str_starts_with($phone, '90')));
            
            if (empty($phones)) {
                $this->lastError = 'Geçerli telefon numarası bulunamadı (905xxxxxxxxx formatında olmalı).';
                Log::warning('WaMessage Hata: Geçerli bir telefon numarası bulunamadı.');
                return false;
            }

            // WaMessage message_type mapping: turkce -> normal (WaMessage Türkçe karakteri otomatik algılar)
            $wamMessageType = 'normal';

            $payload = [
                'api_key' => $apiKey,
                'sender' => $sender,
                'message_type' => $wamMessageType,
                'message' => $message,
                'message_content_type' => $contentType,
                'phones' => $phones,
            ];

            // Ticari iletilerde iptal linki ekle
            if ($contentType === 'ticari') {
                $payload['add_cancel_link'] = true;
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(15)->post($this->apiBaseUrl . '/1toN', $payload);

            $resJson = $response->json();

            if ($response->successful()) {
                // WaMessage başarılı yanıt: {"code": 200, "status": "success", ...}
                if (is_array($resJson) && isset($resJson['status'])) {
                    $status = strtolower((string)$resJson['status']);
                    if ($status === 'success') {
                        Log::info("WaMessage SMS başarıyla gönderildi. ID: " . ($resJson['id'] ?? '-') . ", Kalan kredi: " . ($resJson['quantity'] ?? '-'));
                        return true;
                    }
                    // Status error/false
                    $this->lastError = $resJson['description'] ?? $resJson['message'] ?? $response->body();
                    Log::error("WaMessage API Hatası (sender: '{$sender}'): " . $response->body());
                    return false;
                }
                return true;
            }

            $errMsg = is_array($resJson) ? ($resJson['description'] ?? $resJson['message'] ?? $response->body()) : $response->body();
            $this->lastError = $errMsg ?: 'HTTP ' . $response->status();
            Log::error("WaMessage API Hatası (sender: '{$sender}', HTTP {$response->status()}): " . $response->body());
            return false;

        } catch (\Throwable $th) {
            $this->lastError = $th->getMessage();
            Log::error('WaMessage SMS İstek Hatası: ' . $th->getMessage());
            return false;
        }
    }
}
