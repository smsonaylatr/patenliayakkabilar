<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ElevenLabsService
{
    /**
     * API anahtarını config veya Setting tablosundan çeker
     */
    public static function getApiKey(): ?string
    {
        $key = config('services.elevenlabs.api_key');
        if (!empty($key)) {
            return $key;
        }

        try {
            return Setting::where('key', 'elevenlabs_api_key')->value('value');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * ElevenLabs API'nin aktif ve yapılandırılmış olup olmadığını kontrol eder
     */
    public static function isConfigured(): bool
    {
        return !empty(static::getApiKey());
    }

    /**
     * Türkçe anonslar için en popüler ve doğal ses profilleri
     */
    public static function getVoices(): array
    {
        return [
            '21m00Tcm4TlvDq8ikWAM' => '👩 Zeynep / Rachel (Sıcak & Profesyonel Mağaza Asistanı Kadın)',
            'pNInz6obpgDQGcFmaJgB' => '👨 Can / Adam (Karizmatik & Güven Veren Erkek)',
            'EXAVITQu4vr4xnSDxMaL' => '👧 Elif / Bella (Enerjik & Samimi Genç Kadın)',
            'ErXwobaYiN019PkySvjV' => '🧔 Murat / Antoni (Doğal & Sakin Erkek)',
            'custom' => '✍️ Özel Voice ID Gir...',
        ];
    }

    /**
     * Metni ElevenLabs ile seslendirir, diskte cache'ler ve doğrudan erişilebilir URL döner.
     * Eğer API key yoksa veya istek başarısız olursa null döner (böylece tarayıcı TTS fallback yapar).
     */
    public function generateSpeech(string $text, ?string $voiceId = null): ?string
    {
        $text = trim($text);
        if (empty($text)) {
            return null;
        }

        $apiKey = static::getApiKey();
        if (empty($apiKey)) {
            return null;
        }

        $voiceId = $voiceId ?: config('services.elevenlabs.default_voice_id', '21m00Tcm4TlvDq8ikWAM');
        $modelId = config('services.elevenlabs.model_id', 'eleven_multilingual_v2');

        // Ses dosyasını hashleyerek aynı metin için API kotası harcamadan diskten hızlıca sunalım
        $hash = md5($voiceId . '_' . $modelId . '_' . $text);
        $fileName = "voice-cache/{$hash}.mp3";

        // Cache kontrolü
        if (Storage::disk('public')->exists($fileName)) {
            return asset('storage/' . $fileName);
        }

        try {
            $endpoint = "https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}";

            $response = Http::withHeaders([
                'xi-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'audio/mpeg',
            ])->timeout(12)->post($endpoint, [
                'text' => $text,
                'model_id' => $modelId,
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.8,
                ],
            ]);

            if ($response->successful()) {
                Storage::disk('public')->put($fileName, $response->body());
                return asset('storage/' . $fileName);
            }

            Log::warning('ElevenLabs API isteği başarısız oldu', [
                'status' => $response->status(),
                'error' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('ElevenLabs ses üretim hatası: ' . $e->getMessage());
            return null;
        }
    }
}
