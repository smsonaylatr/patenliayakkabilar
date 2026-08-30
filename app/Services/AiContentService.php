<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiContentService
{
    /**
     * Ürün için AIO özeti, anahtar kelimeler ve Sıkça Sorulan Soruları üretir.
     */
    public function generateProductAio(array $params): array
    {
        $name = trim($params['name'] ?? '');
        $brand = trim($params['brand'] ?? 'KICK SPEED');
        $gender = $params['gender'] ?? 'unisex';
        $ageGroup = $params['age_group'] ?? 'cocuk';
        $shortDesc = trim(strip_tags($params['short_description'] ?? ''));
        $desc = trim(strip_tags($params['description'] ?? ''));

        $genderLabel = match ($gender) {
            'erkek'       => 'Erkek',
            'kadin'       => 'Kadın',
            'erkek_cocuk' => 'Erkek Çocuk',
            'kiz_cocuk'   => 'Kız Çocuk',
            'unisex'      => 'Unisex',
            default       => 'Unisex',
        };

        $ageLabel = match ($ageGroup) {
            'cocuk'    => 'Çocuk',
            'genc'     => 'Genç',
            'yetiskin' => 'Yetişkin',
            default    => 'Çocuk',
        };

        $prompt = "Aşağıdaki patenli ayakkabı ürün bilgilerini inceleyerek bu ürün için:
1. SEO ve AIO (AI Overview / Google SGE) uyumlu net bir TL;DR özeti çıkar (maksimum 2-3 cümle).
2. Hedef AI arama motorları için 5 adet popüler arama niyeti (prompt / keyword) listesi oluştur. Sadece kelime/cümle öbeği listesi (array of strings) ver.
3. Ürünle ilgili müşterilerin sıkça sorabileceği 3 adet soru (question) ve ikna edici cevap (answer) oluştur.

Ürün Adı: {$name}
Marka: {$brand}
Hedef Kitle: {$genderLabel} ({$ageLabel})
Kısa Tanıtım: {$shortDesc}
Detay: {$desc}

Lütfen yanıtını SADECE aşağıdaki JSON formatında ver, başka metin veya markdown backtick ekleme:
{
  \"aio_summary\": \"...\",
  \"aio_target_keywords\": [\"keyword1\", \"keyword2\", \"keyword3\", \"keyword4\", \"keyword5\"],
  \"faq_schema\": [
    { \"question\": \"...\", \"answer\": \"...\" },
    { \"question\": \"...\", \"answer\": \"...\" },
    { \"question\": \"...\", \"answer\": \"...\" }
  ]
}";

        // 1. Gemini API'yi Dene
        $geminiKey = $this->getGeminiKey();
        if (!empty($geminiKey)) {
            $geminiResult = $this->callGemini($prompt, $geminiKey);
            if ($geminiResult['success']) {
                return [
                    'success'             => true,
                    'provider'            => 'Google Gemini',
                    'aio_summary'         => $geminiResult['data']['aio_summary'],
                    'aio_target_keywords' => $geminiResult['data']['aio_target_keywords'] ?? [],
                    'faq_schema'          => $geminiResult['data']['faq_schema'] ?? [],
                    'notice'              => 'Google Gemini AI ile başarıyla üretildi.',
                ];
            }
        }

        // 2. Groq API'yi Dene
        $groqKey = $this->getGroqKey();
        if (!empty($groqKey)) {
            $groqResult = $this->callGroq($prompt, $groqKey);
            if ($groqResult['success']) {
                return [
                    'success'             => true,
                    'provider'            => 'Groq AI (Llama 3.3)',
                    'aio_summary'         => $groqResult['data']['aio_summary'],
                    'aio_target_keywords' => $groqResult['data']['aio_target_keywords'] ?? [],
                    'faq_schema'          => $groqResult['data']['faq_schema'] ?? [],
                    'notice'              => 'Groq AI ile başarıyla üretildi.',
                ];
            }
        }

        // 3. OpenAI API'yi Dene
        $openAiKey = $this->getOpenAiKey();
        $openAiError = null;
        if (!empty($openAiKey)) {
            $openAiResult = $this->callOpenAI($prompt, $openAiKey);
            if ($openAiResult['success']) {
                return [
                    'success'             => true,
                    'provider'            => 'OpenAI GPT',
                    'aio_summary'         => $openAiResult['data']['aio_summary'],
                    'aio_target_keywords' => $openAiResult['data']['aio_target_keywords'] ?? [],
                    'faq_schema'          => $openAiResult['data']['faq_schema'] ?? [],
                    'notice'              => 'OpenAI GPT ile başarıyla üretildi.',
                ];
            } else {
                $openAiError = $openAiResult['error'] ?? null;
            }
        }

        // 4. Akıllı Yerel E-Ticaret Şablon Motoru (Local Intelligent Fallback)
        $fallbackData = $this->generateLocalFallback($name, $brand, $genderLabel, $ageLabel, $shortDesc);
        
        $notice = 'Akıllı E-Ticaret motoru ile AIO verileri başarıyla üretildi.';
        if ($openAiError && (str_contains($openAiError, 'insufficient_quota') || str_contains($openAiError, 'credit_balance_exhausted'))) {
            $notice = 'OpenAI API krediniz tükendiği için AIO verileri Akıllı E-Ticaret motorumuz tarafından otomatik olarak üretildi.';
        }

        return [
            'success'             => true,
            'provider'            => 'Smart Local Engine',
            'aio_summary'         => $fallbackData['aio_summary'],
            'aio_target_keywords' => $fallbackData['aio_target_keywords'],
            'faq_schema'          => $fallbackData['faq_schema'],
            'notice'              => $notice,
        ];
    }

    /**
     * Gemini API Çağrısı
     */
    private function callGemini(string $prompt, string $apiKey): array
    {
        try {
            $response = Http::timeout(25)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt . "\n\nJSON formatında yanıt ver."],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature'      => 0.7,
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                $parsed = $this->cleanAndParseJson($content);
                if ($parsed && isset($parsed['aio_summary'])) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            Log::warning('Gemini API Error: ' . $response->body());
            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::warning('Gemini API Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * OpenAI API Çağrısı
     */
    private function callOpenAI(string $prompt, string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)->timeout(25)->post('https://api.openai.com/v1/chat/completions', [
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Sen uzman bir SEO ve E-ticaret asistanısın. Sadece geçerli bir JSON objesi döndür. Markdown backtick kullanma.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $parsed = $this->cleanAndParseJson($content);
                if ($parsed && isset($parsed['aio_summary'])) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Groq API Çağrısı
     */
    private function callGroq(string $prompt, string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)->timeout(25)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Sen uzman bir SEO ve E-ticaret asistanısın. Sadece geçerli bir JSON objesi döndür.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $parsed = $this->cleanAndParseJson($content);
                if ($parsed && isset($parsed['aio_summary'])) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Akıllı Yerel E-Ticaret Şablon Üreticisi
     */
    public function generateLocalFallback(string $name, string $brand, string $genderLabel, string $ageLabel, string $shortDesc = ''): array
    {
        $cleanName = preg_replace('/\s*\(Kopya\)/i', '', $name);
        
        $summary = "{$brand} {$cleanName}, {$genderLabel} ({$ageLabel}) için özel olarak tasarlanmış, bas-çek gizlenebilir tekerlek mekanizmasına sahip şık ve konforlu bir patenli ayakkabıdır. Günlük spor ayakkabı konforunu ve paten eğlencesini tek bir modelde bir araya getirir.";
        
        $keywords = [
            "{$cleanName} fiyatı",
            "{$genderLabel} patenli ayakkabı",
            "{$brand} paten ayakkabı",
            "gizlenebilir tekerlekli spor ayakkabı",
            "{$ageLabel} tekerlekli ayakkabı modelleri",
        ];

        $faq = [
            [
                'question' => 'Tekerlekler nasıl açılır ve gizlenir?',
                'answer'   => 'Topuk arkasında yer alan güvenlik kilitli butona basarak tekerlekleri kolayca açabilir, dilerseniz tek bir dokunuşla taban içerisine kilitleyerek normal bir spor ayakkabı olarak kullanabilirsiniz.',
            ],
            [
                'question' => 'Kalıpları tam mı, hangi numarayı almalıyım?',
                'answer'   => 'Ürünlerimiz standart tam kalıptır. Günlük giyilen spor ayakkabı numarasını tercih edebilirsiniz. Büyüme çağındaki çocuklar için 1 numara büyük tercih edilmesi tavsiye edilir.',
            ],
            [
                'question' => 'Tekerlekler dayanıklı mı ve kayma güvenliği nasıl?',
                'answer'   => 'Yüksek dayanımlı PU tekerlekler ve kaymaz kauçuk taban yapısına sahiptir. Düz zeminlerde üstün tutuş ve güvenli bir kayma deneyimi sunar.',
            ],
        ];

        return [
            'aio_summary'         => $summary,
            'aio_target_keywords' => $keywords,
            'faq_schema'          => $faq,
        ];
    }

    /**
     * JSON metnini temizleyip array'e çevirir.
     */
    private function cleanAndParseJson(?string $content): ?array
    {
        if (empty($content)) {
            return null;
        }

        $cleaned = trim(preg_replace('/```json\s*|\s*```/', '', $content));
        
        if (!str_starts_with($cleaned, '{')) {
            $start = strpos($cleaned, '{');
            $end = strrpos($cleaned, '}');
            if ($start !== false && $end !== false) {
                $cleaned = substr($cleaned, $start, $end - $start + 1);
            }
        }

        $decoded = json_decode($cleaned, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function getGeminiKey(): ?string
    {
        $key = Setting::where('key', 'gemini_api_key')->value('value') 
            ?: env('GEMINI_API_KEY') 
            ?: env('GOOGLE_API_KEY') 
            ?: env('GOOGLE_AI_KEY');

        return !empty($key) && $key !== 'your-gemini-api-key' ? trim($key) : null;
    }

    private function getOpenAiKey(): ?string
    {
        $key = Setting::where('key', 'openai_api_key')->value('value') 
            ?: env('OPENAI_API_KEY');

        return !empty($key) && $key !== 'sk-your-openai-api-key-here' ? trim($key) : null;
    }

    private function getGroqKey(): ?string
    {
        $key = Setting::where('key', 'groq_api_key')->value('value') 
            ?: env('GROQ_API_KEY');

        return !empty($key) && $key !== 'your-groq-api-key' ? trim($key) : null;
    }
}
