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
    protected function getGibClient(): \Mlevent\Fatura\Gib
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
        // İptal edilen siparişlere fatura kesilmez
        if ($order->status === 'cancelled') {
            return ['success' => false, 'message' => 'İptal edilmiş siparişe fatura kesilmez.'];
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

            $isCorporate = strlen(trim($taxNumber)) === 10 || !empty($companyName);
            
            $nameParts = explode(' ', trim($customerName));
            $surname = count($nameParts) > 1 ? array_pop($nameParts) : '';
            $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : $customerName;

            $date = date('d/m/Y');
            $time = date('H:i:s');

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
                adres:            $order->billing_address ?: ($order->shipping_address ?: 'Türkiye'),
                mahalleSemtIlce:  $order->billing_district ?: ($order->shipping_district ?: 'Merkez'),
                sehir:            $order->billing_city ?: ($order->shipping_city ?: 'İstanbul'),
                ulke:             'Türkiye',
                tel:              $order->customer_phone ?: '',
                eposta:           $order->customer_email ?: '',
                websitesi:        'https://patenliayakkabilar.com',
                vergiDairesi:     $taxOffice,
                not:              $invoiceNote,
            );

            // Yeni Fiyatlama Mantığı
            $isCOD = ($order->payment_method === 'cash_on_delivery');
            
            // Ürün fiyatı her zaman paneldeki "Genel Toplam" kadar olacak (Örn: 2000 TL)
            $productGrossTotal = (float)$order->grand_total;
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

            $gib->logout();

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
        }
    }

    /**
     * GİB Fatura HTML içeriğine Siteden Dinamik Çekilen Firma Logosu ve Başlık ekler
     */
    protected function enrichInvoiceHtmlWithLogo(string $html): string
    {
        $logoSrc = $this->getSiteLogoUrl();
        
        $logoHeaderHtml = <<<HTML
<div class="brand-invoice-header" style="background: linear-gradient(to right, #f8fafc, #ffffff); border-bottom: 4px solid #0f172a; padding: 35px 50px; margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box;">
    <div style="display: flex; align-items: center; gap: 24px;">
        <div style="background: #ffffff; padding: 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
            <img src="{$logoSrc}" alt="Firma Logosu" style="height: 70px; width: 70px; object-fit: contain;">
        </div>
        <div>
            <h1 style="margin: 0; color: #0f172a; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; text-transform: uppercase;">{$this->companyName}</h1>
            <p style="margin: 6px 0 0; color: #64748b; font-size: 13px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;">Resmİ E-Arşİv Faturası</p>
        </div>
    </div>
    <div style="text-align: right;">
        <span style="display: inline-block; padding: 10px 18px; background: #0f172a; color: #ffffff; font-size: 14px; font-weight: 700; border-radius: 6px; letter-spacing: 1px;">
            PATENLİAYAKKABİLAR.COM
        </span>
    </div>
</div>
HTML;

        if (stripos($html, '<body') !== false) {
            $html = preg_replace('/(<body[^>]*>)/i', '$1' . "\n" . $logoHeaderHtml, $html, 1);
        } else {
            $html = $logoHeaderHtml . "\n" . $html;
        }

        return $html;
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
        try {
            $gib = $this->getGibClient();
            // Önce imzalanmış versiyonu dene, bulunamazsa taslak versiyonunu çek
            $html = null;
            try {
                $html = $gib->getHtml($uuid, true);
            } catch (\Throwable $e) {
                $html = $gib->getHtml($uuid, false);
            }
            $gib->logout();

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
        }
    }
}
