<?php

namespace App\Services;

use App\Mail\GibInvoiceMail;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GibEArsivService
{
    protected string $userCode;
    protected string $password;
    protected bool $isTest;
    protected string $companyName;
    protected string $companyVkn;
    protected string $companyTaxOffice;
    protected string $companyAddress;

    public function __construct()
    {
        $settings = Setting::whereIn('key', [
            'gib_user_code',
            'gib_password',
            'gib_test_mode',
            'gib_company_name',
            'gib_company_vkn',
            'gib_company_tax_office',
            'gib_company_address',
        ])->pluck('value', 'key')->toArray();

        $this->userCode = $settings['gib_user_code'] ?? config('gib.user_code', '');
        $this->password = $settings['gib_password'] ?? config('gib.password', '');
        $this->isTest = filter_var($settings['gib_test_mode'] ?? config('gib.is_test', true), FILTER_VALIDATE_BOOLEAN);

        $this->companyName = $settings['gib_company_name'] ?? config('gib.company_name', 'Patenli Ayakkabılar');
        $this->companyVkn = $settings['gib_company_vkn'] ?? config('gib.company_vkn', '1111111111');
        $this->companyTaxOffice = $settings['gib_company_tax_office'] ?? config('gib.company_tax_office', 'Kadıköy');
        $this->companyAddress = $settings['gib_company_address'] ?? config('gib.company_address', 'İstanbul');
    }

    /**
     * Siteden dinamik firma logosu URL'sini çeker
     */
    public function getSiteLogoUrl(): string
    {
        $customLogo = Setting::where('key', 'gib_logo_url')->value('value');
        if (!empty($customLogo)) {
            return $customLogo;
        }

        return url('/favicon.png');
    }

    /**
     * Autoloader kaydet — Octane altında Composer autoload bazen Mlevent\Fatura'yı bulamıyor
     */
    protected function ensureAutoloader(): void
    {
        if (class_exists(\Mlevent\Fatura\Models\InvoiceModel::class)) {
            return;
        }

        spl_autoload_register(function ($class) {
            if (str_starts_with($class, 'Mlevent\\Fatura\\')) {
                $relative = str_replace('Mlevent\\Fatura\\', '', $class);
                $file = base_path('vendor/mlevent/fatura/src/' . str_replace('\\', '/', $relative) . '.php');
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });

        $helpersFile = base_path('vendor/mlevent/fatura/src/Utils/Helpers.php');
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    /**
     * GİB E-Arşiv Portal Giriş Nesnesi Oluşturur
     */
    public function getGibClient(): \Mlevent\Fatura\Gib
    {
        $this->ensureAutoloader();

        if (!class_exists(\Mlevent\Fatura\Gib::class)) {
            throw new \Exception("mlevent/fatura kütüphanesi yüklü değil. 'composer require mlevent/fatura' komutunu çalıştırın.");
        }

        $gib = new \Mlevent\Fatura\Gib();

        if ($this->isTest) {
            if (!empty($this->userCode) && !empty($this->password)) {
                $gib->setTestCredentials($this->userCode, $this->password);
            } else {
                // GİB Test portalı dinamik hesapları bozabiliyor, hardcode eski test hesabı
                $gib->setTestCredentials('33333333', '123456');
            }
        } else {
            if (empty($this->userCode) || empty($this->password)) {
                throw new \Exception("GİB E-Arşiv Kullanıcı Kodu veya Parolası girilmemiş. Admin > E-Arşiv Ayarları sayfasından giriş bilgilerinizi tanımlayın.");
            }
            $gib->setCredentials($this->userCode, $this->password);
        }

        $gib->login();

        return $gib;
    }

    /**
     * Bağlantıyı test eder
     */
    public function testConnection(): array
    {
        try {
            $gib = $this->getGibClient();
            $token = $gib->getToken();
            $gib->logout();
            return [
                'success' => true,
                'message' => 'GİB E-Arşiv Portal bağlantısı başarılı! Token alındı.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'GİB Portal bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tam Otomatik Fatura Oluşturma ve Müşteriye Mail Gönderme (HER ZAMAN AKTİF)
     */
    public function autoInvoiceAndSendMail(Order $order): array
    {
        // Sadece teslim edildi olan siparişlere fatura kesilebilir
        if ($order->status !== 'delivered') {
            return ['success' => false, 'message' => 'Sadece teslim edilen siparişlerin faturası kesilebilir.'];
        }

        if ($order->is_invoiced) {
            // Zaten fatura kesilmişse maili tekrar göndermeyi dene
            if (!empty($order->customer_email)) {
                $this->sendInvoiceMail($order);
            }
            return ['success' => true, 'message' => 'Sipariş zaten faturalandırılmış. Mail iletildi.'];
        }

        // Siparişin items ilişkisini yükle
        $order->loadMissing(['items.product', 'items.variant']);

        // Faturayı Kes
        $result = $this->createInvoice($order);

        // Otomatik Olarak Müşteriye Mail Gönder
        if ($result['success'] && !empty($order->customer_email)) {
            $order->refresh();
            $this->sendInvoiceMail($order);
        }

        return $result;
    }

    /**
     * Sipariş için GİB E-Arşiv Taslak Faturası Oluşturur
     * mlevent/fatura kütüphanesi InvoiceModel + InvoiceItemModel + createDraft API'sini kullanır
     */
    public function createInvoice(Order $order, array $overrideData = []): array
    {
        // Sadece teslim edildi olan siparişlere fatura kesilebilir
        if ($order->status !== 'delivered') {
            return [
                'success' => false,
                'message' => 'Fatura kesebilmek için sipariş durumunun "Teslim Edildi" olması gerekir.',
            ];
        }

        try {
            $this->ensureAutoloader();

            $taxNumber = $overrideData['tax_number'] ?? $order->tax_number ?? '11111111111';
            $taxOffice = $overrideData['tax_office'] ?? $order->tax_office ?? '';
            $customerName = $overrideData['customer_name'] ?? $order->customer_name ?? 'Müşteri';
            $companyName = $overrideData['company_name'] ?? $order->company_name ?? '';
            $kdvPercent = (float)($overrideData['kdv_rate'] ?? 10);
            $order->loadMissing(['items.product', 'items.variant']);
            
            $productNames = $order->items->map(function($item) {
                return $item->product_name ?? ('Ürün #' . $item->product_id);
            })->implode(', ');
            
            $invoiceNote = $overrideData['invoice_note'] ?? ("Sipariş No: #" . $order->order_number . " - " . $productNames);

            // Kurumsal/Bireysel ayrımı: önce order->invoice_type, yoksa eski heuristic
            $isCorporate = ($order->invoice_type === 'corporate')
                || strlen(trim($taxNumber)) === 10
                || !empty($companyName);
            
            $nameParts = explode(' ', trim($customerName));
            $surname = count($nameParts) > 1 ? array_pop($nameParts) : '';
            $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : $customerName;

            $date = date('d/m/Y');
            $time = date('H:i:s');

            $rawAddress = trim($order->billing_address ?: ($order->shipping_address ?: 'Türkiye'));
            $binaNo = '-';
            $kapiNo = '-';

            // Bina No parsing
            if (preg_match('/(?:no|numara)\s*[:\.]?\s*([0-9a-zA-Z]+)/i', $rawAddress, $matches)) {
                $binaNo = $matches[1];
                $rawAddress = preg_replace('/(?:no|numara)\s*[:\.]?\s*[0-9a-zA-Z]+/i', '', $rawAddress);
            }

            // Kapı No parsing
            if (preg_match('/(?:daire|d|kapi|kapı)\s*[:\.]?\s*([0-9a-zA-Z]+)/i', $rawAddress, $matches)) {
                $kapiNo = $matches[1];
                $rawAddress = preg_replace('/(?:daire|d|kapi|kapı)\s*[:\.]?\s*[0-9a-zA-Z]+/i', '', $rawAddress);
            }

            $rawAddress = trim(preg_replace('/[\s,]+/', ' ', $rawAddress));
            if (empty($rawAddress) || $rawAddress === '-') {
                $rawAddress = 'Merkez';
            }

            // InvoiceModel oluştur (mlevent/fatura model yapısı)
            $invoice = new \Mlevent\Fatura\Models\InvoiceModel(
                vknTckn:          $taxNumber,
                tarih:            $date,
                saat:             $time,
                faturaTipi:       \Mlevent\Fatura\Enums\InvoiceType::Satis,
                siparisNumarasi:  $order->order_number,
                siparisTarihi:    $order->created_at ? $order->created_at->format('d/m/Y') : $date,
                aliciUnvan:       $isCorporate ? ($companyName ?: $customerName) : '',
                aliciAdi:         !$isCorporate ? $firstName : '',
                aliciSoyadi:      !$isCorporate ? $surname : '',
                adres:            $rawAddress,
                binaNo:           $binaNo,
                kapiNo:           $kapiNo,
                mahalleSemtIlce:  $order->billing_district ?: ($order->shipping_district ?: 'Merkez'),
                sehir:            $order->billing_city ?: ($order->shipping_city ?: 'İstanbul'),
                ulke:             'Türkiye',
                tel:              $order->customer_phone ?: '',
                eposta:           $order->customer_email ?: '',
                websitesi:        '',
                vergiDairesi:     $taxOffice,
                not:              $invoiceNote,
            );

            // Yeni Fiyatlama Mantığı
            $isCOD = ($order->payment_method === 'cash_on_delivery');
            
            // Eğer kapıda ödeme ise toplam tutardan 200 TL kargo bedeli düşüp ürüne yansıtıyoruz
            if ($isCOD) {
                $productGrossTotal = (float)$order->grand_total - 200;
            } else {
                $productGrossTotal = (float)$order->grand_total;
            }

            if ($productGrossTotal < 0) {
                $productGrossTotal = 0;
            }
            
            $productKdvPercent = $kdvPercent; // 10%
            $productUnitPriceExVat = $productGrossTotal / (1 + ($productKdvPercent / 100));

            // Tüm ürünleri tek kalem olarak ekle
            $invoice->addItem(
                new \Mlevent\Fatura\Models\InvoiceItemModel(
                    malHizmet:  $productNames ?: 'Ürün Bedeli',
                    miktar:     1.0,
                    birimFiyat: round($productUnitPriceExVat, 4),
                    kdvOrani:   $productKdvPercent,
                )
            );

            // Kapıda Ödeme siparişlerinde ekstra 200 TL Kargo Hizmet Bedeli (KDV %20)
            if ($isCOD) {
                $shippingKdvPercent = 20;
                $shippingGrossTotal = 200;
                $shippingExVat = $shippingGrossTotal / (1 + ($shippingKdvPercent / 100));

                $invoice->addItem(
                    new \Mlevent\Fatura\Models\InvoiceItemModel(
                        malHizmet:  'Kargo Hizmet Bedeli',
                        miktar:     1.0,
                        birimFiyat: round($shippingExVat, 4),
                        kdvOrani:   $shippingKdvPercent,
                    )
                );
            }

            // GİB Portalına Gönder (createDraft = taslak fatura oluştur)
            $gib = $this->getGibClient();
            $gib->createDraft($invoice);

            // Oluşturulan faturanın UUID'sini al
            $uuid = $gib->lastId();

            // Fatura HTML'ini çek (taslak olduğu için signed=false)
            $html = null;
            try {
                $html = $gib->getHtml($uuid, false);
                if ($html) {
                    $html = $this->enrichInvoiceHtmlWithLogo($html);
                }
            } catch (\Throwable $hEx) {
                Log::warning("GİB Fatura HTML çekilemedi (UUID: {$uuid}): " . $hEx->getMessage());
            }

            // Veritabanını Güncelle
            $order->update([
                'tax_number'         => $taxNumber,
                'tax_office'         => $taxOffice,
                'company_name'       => $companyName,
                'is_invoiced'        => true,
                'gib_invoice_uuid'   => $uuid,
                'gib_invoice_date'   => now(),
                'gib_invoice_status' => 'draft',
                'gib_invoice_html'   => $html,
                'gib_invoice_error'  => null,
            ]);

            Log::info("GİB E-Arşiv Faturası oluşturuldu. Sipariş No: #{$order->order_number}, UUID: {$uuid}");

            return [
                'success' => true,
                'uuid'    => $uuid,
                'message' => 'GİB E-Arşiv Faturası başarıyla oluşturuldu.',
            ];

        } catch (\Mlevent\Fatura\Exceptions\ApiException $e) {
            $errorMessage = $e->getMessage();
            $detailedError = $errorMessage;
            if ($e->hasResponse()) {
                $detailedError .= " - Response: " . print_r($e->getResponse(), true);
            }
            Log::error("GİB E-Arşiv API Hatası. Sipariş No: #{$order->order_number}: " . $detailedError);

            try {
                $order->update([
                    'gib_invoice_status' => 'failed',
                    'gib_invoice_error'  => mb_substr($detailedError, 0, 1000),
                ]);
            } catch (\Throwable $dbEx) {
                Log::error("GİB hata kaydı DB güncelleme hatası: " . $dbEx->getMessage());
            }

            return [
                'success' => false,
                'message' => 'GİB API Hatası: ' . $errorMessage,
            ];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("GİB E-Arşiv Fatura Oluşturma Hatası. Sipariş No: #{$order->order_number}: " . $errorMessage);

            try {
                $order->update([
                    'gib_invoice_status' => 'failed',
                    'gib_invoice_error'  => mb_substr($errorMessage, 0, 500),
                ]);
            } catch (\Throwable $dbEx) {
                Log::error("GİB hata kaydı DB güncelleme hatası: " . $dbEx->getMessage());
            }

            return [
                'success' => false,
                'message' => 'Fatura oluşturulurken hata oluştu: ' . $errorMessage,
            ];
        } finally {
            if (isset($gib) && $gib) {
                try {
                    $gib->logout();
                } catch (\Throwable $t) {
                    // Ignore logout errors
                }
            }
        }
    }

    /**
     * GİB Fatura HTML içeriğine Siteden Dinamik Çekilen Firma Logosu ve Başlık ekler
     */
    protected function enrichInvoiceHtmlWithLogo(string $html): string
    {
        $logoUrl = 'https://i.hizliresim.com/ixv8t7k1.png';
        
        $logoHtml = <<<HTML
<div style="text-align: left; padding-top: 2px; padding-bottom: 0px; margin-top: -5px;">
    <img src="{$logoUrl}" alt="Firma Logosu" style="max-width: 250px; height: auto; object-fit: contain;">
</div>
</td>
HTML;

        // "Satıcı" tablosunun bittiği yeri bul (</table><hr></td>) ve hemen altına logoyu ekle.
        // GİB HTML'sinde Satıcı alanından sonra orta sütun başlar: <td valign="middle"
        $html = preg_replace('/(<\/table>\s*<hr>\s*<\/td>)(\s*<td[^>]*valign="middle")/i', '</table><hr>' . $logoHtml . '$2', $html, 1);

        return $html;
    }

    /**
     * GİB Fatura HTML belgesini yüksek kaliteli PDF çıktısına dönüştürür
     */
    public static function convertHtmlToPdf(string $html): ?string
    {
        try {
            // XML başlığını kaldır (DomPDF HTML parser ile çakışabilir)
            $cleanHtml = preg_replace('/<\?xml.*?\?>/i', '', $html);

            // Tüm JavaScript ve noscript bloklarını kaldır (Gmail'de metin olarak görünen qrcode.min.js vb.)
            $cleanHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleanHtml);
            $cleanHtml = preg_replace('/<noscript\b[^>]*>(.*?)<\/noscript>/is', '', $cleanHtml);

            // UTF-8 karakter desteği garanti altına alınsın
            if (!str_contains($cleanHtml, 'charset=')) {
                $cleanHtml = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $cleanHtml;
            }

            // PDF çıktısı için sayfa ve font optimizasyonları
            $customCss = <<<CSS
<style>
    @page {
        margin: 8mm 8mm 8mm 8mm;
        size: A4 portrait;
    }
    * {
        font-family: 'DejaVu Sans', sans-serif !important;
    }
    body {
        font-size: 10px !important;
        line-height: 1.25 !important;
        color: #111111 !important;
        background: #ffffff !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    img {
        max-width: 100% !important;
        height: auto !important;
    }
</style>
CSS;
            if (stripos($cleanHtml, '</head>') !== false) {
                $cleanHtml = str_ireplace('</head>', $customCss . '</head>', $cleanHtml);
            } else {
                $cleanHtml = $customCss . $cleanHtml;
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($cleanHtml);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
            ]);

            $output = $pdf->output();
            return !empty($output) ? $output : null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("GİB Fatura convertHtmlToPdf Hatası: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sipariş için garantili ve hatasız PDF fatura belgesi üretir.
     * Orijinal GİB HTML'i varsa onu PDF'e çevirir; yoksa veya dönüştürülemezse
     * kurumsal ve resmi standartlardaki Blade fatura şablonundan gerçek PDF oluşturur.
     * Asla ve kesinlikle HTML metni döndürmez!
     */
    public static function generateInvoicePdf(Order $order): ?string
    {
        // 1. GİB portalı HTML'i varsa öncelikle onu PDF'e çevirmeyi dene
        $html = $order->gib_invoice_html;
        if (empty($html) && !empty($order->gib_invoice_uuid)) {
            try {
                $service = app(self::class);
                $html = $service->getInvoiceHtml($order->gib_invoice_uuid);
                if ($html) {
                    $order->update(['gib_invoice_html' => $html]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("GİB HTML alma hatası: " . $e->getMessage());
            }
        }

        if (!empty($html)) {
            $pdf = self::convertHtmlToPdf($html);
            if (!empty($pdf)) {
                return $pdf;
            }
        }

        // 2. GİB HTML'i yoksa veya dönüştürme başarısız olursa,
        // kusursuz tasarlanmış Blade şablonundan gerçek ve şık PDF üret
        try {
            $order->loadMissing(['items.product', 'items.variant']);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
            ]);

            return $pdf->output();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Blade Fatura PDF oluşturma hatası: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Faturayı Müşteriye E-Posta Olarak Gönderir
     */
    public function sendInvoiceMail(Order $order): bool
    {
        try {
            if (empty($order->customer_email)) {
                return false;
            }

            Mail::to($order->customer_email)->send(new GibInvoiceMail($order));
            Log::info("GİB E-Arşiv Faturası e-postası müşteriye gönderildi (#{$order->order_number} -> {$order->customer_email})");
            return true;
        } catch (\Throwable $e) {
            Log::error("GİB Fatura E-Posta Gönderim Hatası (#{$order->order_number}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * GİB Portalından Fatura HTML belgesini çeker
     */
    public function getInvoiceHtml(string $uuid): ?string
    {
        $gib = null;
        try {
            $gib = $this->getGibClient();
            // Önce imzalanmış versiyonu dene, bulunamazsa taslak versiyonunu çek
            $html = null;
            try {
                $html = $gib->getHtml($uuid, true);
            } catch (\Throwable $e) {
                $html = $gib->getHtml($uuid, false);
            }

            if ($html) {
                $html = $this->enrichInvoiceHtmlWithLogo($html);
            }

            return $html;
        } catch (\Mlevent\Fatura\Exceptions\ApiException $e) {
            $detailedError = $e->getMessage();
            if ($e->hasResponse()) {
                $detailedError .= " - Response: " . print_r($e->getResponse(), true);
            }
            Log::error("GİB Fatura HTML Alma Hatası API (UUID: {$uuid}): " . $detailedError);
            return null;
        } catch (\Throwable $e) {
            Log::error("GİB Fatura HTML Alma Hatası (UUID: {$uuid}): " . $e->getMessage());
            return null;
        } finally {
            if ($gib) {
                try {
                    $gib->logout();
                } catch (\Throwable $t) {
                    // Ignore logout errors
                }
            }
        }
    }

    /**
     * SMS Doğrulamayı Başlatır (GİB'den telefona SMS gönderir)
     * Timeout hatalarında otomatik retry yapar
     */
    public function startSmsVerification(): array
    {
        $maxRetries = 3;
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $gib = null;
            try {
                $gib = $this->getGibClient();
                $operationId = $gib->startSmsVerification();
                
                return [
                    'success' => true,
                    'operation_id' => $operationId,
                ];
            } catch (\Mlevent\Fatura\Exceptions\ApiException $e) {
                $error = $e->getMessage();
                if ($e->hasResponse()) {
                    $error .= " - Response: " . print_r($e->getResponse(), true);
                }
                $lastError = 'GİB API Hatası: ' . $error;
                break; // API hataları retry'a tabi değil
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning("GİB SMS Doğrulama - Deneme {$attempt}/{$maxRetries} başarısız: " . $lastError);
                
                // Timeout veya bağlantı hatası ise retry yap
                if ($attempt < $maxRetries && (str_contains($lastError, 'cURL error') || str_contains($lastError, 'timed out') || str_contains($lastError, 'Connection'))) {
                    sleep(2); // 2 saniye bekle ve tekrar dene
                    continue;
                }
                break;
            } finally {
                if ($gib) {
                    try {
                        $gib->logout();
                    } catch (\Throwable $t) {
                        // Ignore logout errors
                    }
                }
            }
        }

        return [
            'success' => false,
            'message' => 'SMS doğrulama başlatılamadı: ' . $lastError,
        ];
    }

    /**
     * SMS Kodunu GİB'e göndererek belgeleri imzalar
     * Timeout hatalarında otomatik retry yapar
     */
    public function completeSmsVerification(string $smsCode, string $operationId, array $uuids): array
    {
        $smsCode = strtoupper(trim($smsCode));
        $maxRetries = 3;
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $gib = null;
            try {
                $gib = $this->getGibClient();
                $result = $gib->completeSmsVerification($smsCode, $operationId, $uuids);
                $count = $gib->rowCount();
                
                return [
                    'success' => $result,
                    'count' => $count,
                    'message' => $result ? "{$count} adet belge başarıyla imzalandı." : "İmzalama başarısız oldu.",
                ];
            } catch (\Mlevent\Fatura\Exceptions\ApiException $e) {
                $error = $e->getMessage();
                if ($e->hasResponse()) {
                    $error .= " - Response: " . print_r($e->getResponse(), true);
                }
                $lastError = 'GİB API Hatası: ' . $error;
                break; // API hataları retry'a tabi değil
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning("GİB SMS Onaylama - Deneme {$attempt}/{$maxRetries} başarısız: " . $lastError);
                
                // Timeout veya bağlantı hatası ise retry yap
                if ($attempt < $maxRetries && (str_contains($lastError, 'cURL error') || str_contains($lastError, 'timed out') || str_contains($lastError, 'Connection'))) {
                    sleep(2); // 2 saniye bekle ve tekrar dene
                    continue;
                }
                break;
            } finally {
                if ($gib) {
                    try {
                        $gib->logout();
                    } catch (\Throwable $t) {
                        // Ignore logout errors
                    }
                }
            }
        }

        return [
            'success' => false,
            'message' => 'SMS onaylama hatası: ' . $lastError,
        ];
    }
}
