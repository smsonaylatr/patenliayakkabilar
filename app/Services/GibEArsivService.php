<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * GİB E-Arşiv Portal Giriş Nesnesi Oluşturur
     */
    protected function getGibClient()
    {
        if (!class_exists(\Mlevent\Fatura\Gib::class)) {
            throw new \Exception("mlevent/fatura kütüphanesi yüklü değil.");
        }

        $gib = new \Mlevent\Fatura\Gib();

        if ($this->isTest) {
            // Test modunda varsayılan test kimlik bilgileri veya girilen test bilgileri kullanılır
            if (!empty($this->userCode) && !empty($this->password)) {
                $gib->setCredentials($this->userCode, $this->password);
            } else {
                $gib->setTestCredentials('33333333', '123456');
            }
        } else {
            if (empty($this->userCode) || empty($this->password)) {
                throw new \Exception("GİB E-Arşiv Kullanıcı Kodu veya Parolası girilmemiş.");
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
            $gib->logout();
            return [
                'success' => true,
                'message' => 'GİB E-Arşiv Portal bağlantısı başarılı!',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'GİB Portal bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sipariş için GİB E-Arşiv Taslak Faturası Oluşturur
     */
    public function createInvoice(Order $order, array $overrideData = []): array
    {
        try {
            $taxNumber = $overrideData['tax_number'] ?? $order->tax_number ?? '11111111111';
            $taxOffice = $overrideData['tax_office'] ?? $order->tax_office ?? '';
            $customerName = $overrideData['customer_name'] ?? $order->customer_name ?? 'Müşteri';
            $companyName = $overrideData['company_name'] ?? $order->company_name ?? '';
            $kdvPercent = (float)($overrideData['kdv_rate'] ?? 20);
            $invoiceNote = $overrideData['invoice_note'] ?? ("Sipariş No: #" . $order->order_number);

            // Müşteri Ad/Soyad Ayrımı (TCKN 11 haneli şahıs ise ad-soyad, VKN 10 haneli kurumsal ise unvan)
            $isCorporate = strlen(trim($taxNumber)) === 10 || !empty($companyName);
            
            $nameParts = explode(' ', trim($customerName));
            $surname = count($nameParts) > 1 ? array_pop($nameParts) : '';
            $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : $customerName;

            $uuid = (string) Str::uuid();
            $date = date('d/m/Y');
            $time = date('H:i:s');

            // Kalemler ve Matrah Hesabı
            $items = [];
            $totalTaxable = 0;
            $totalKdv = 0;

            foreach ($order->items as $item) {
                // Fiyat KDV dahil kabul edilip KDV ayrıştırılır
                $itemTotalGross = (float) $item->price * (int) $item->quantity;
                $itemTaxable = $itemTotalGross / (1 + ($kdvPercent / 100));
                $itemKdv = $itemTotalGross - $itemTaxable;

                $totalTaxable += $itemTaxable;
                $totalKdv += $itemKdv;

                $items[] = [
                    'name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'unit' => 'C62', // Adet / Adet birimi (UN/ECE)
                    'unitPrice' => round($itemTaxable / $item->quantity, 4),
                    'price' => round($itemTaxable, 2),
                    'vatRate' => $kdvPercent,
                    'vatAmount' => round($itemKdv, 2),
                ];
            }

            // Kargo Ücreti Varsa Kalem Olarak Ekle
            if ((float) $order->shipping_price > 0) {
                $shippingGross = (float) $order->shipping_price;
                $shippingTaxable = $shippingGross / (1 + ($kdvPercent / 100));
                $shippingKdv = $shippingGross - $shippingTaxable;

                $totalTaxable += $shippingTaxable;
                $totalKdv += $shippingKdv;

                $items[] = [
                    'name' => 'Kargo Hizmet Bedeli',
                    'quantity' => 1,
                    'unit' => 'C62',
                    'unitPrice' => round($shippingTaxable, 4),
                    'price' => round($shippingTaxable, 2),
                    'vatRate' => $kdvPercent,
                    'vatAmount' => round($shippingKdv, 2),
                ];
            }

            $grandTotal = round($totalTaxable + $totalKdv, 2);

            $invoiceData = [
                'faturaUuid' => $uuid,
                'belgeNumarasi' => '',
                'faturaTarihi' => $date,
                'saat' => $time,
                'parabirimi' => 'TRY',
                'dovizKuru' => 1,
                'faturaTipi' => 'SATIS',
                'vknTckn' => $taxNumber,
                'aliciUnvan' => $isCorporate ? ($companyName ?: $customerName) : '',
                'aliciAdi' => !$isCorporate ? $firstName : '',
                'aliciSoyadi' => !$isCorporate ? $surname : '',
                'binaAdı' => '',
                'binaNo' => '',
                'kapiNo' => '',
                'kasabaKoy' => '',
                'vergiDairesi' => $taxOffice,
                'ulke' => 'Türkiye',
                'bulvarCaddeSokak' => $order->billing_address ?: ($order->shipping_address ?: 'Türkiye'),
                'mahalleSemtiIlce' => $order->billing_district ?: ($order->shipping_district ?: 'Merkez'),
                'sehir' => $order->billing_city ?: ($order->shipping_city ?: 'İstanbul'),
                'postaKodu' => '',
                'tel' => $order->customer_phone ?: '',
                'fax' => '',
                'eposta' => $order->customer_email ?: '',
                'websitesi' => 'https://patenliayakkabilar.com',
                'siparisNumarasi' => $order->order_number,
                'siparisTarihi' => $order->created_at ? $order->created_at->format('d/m/Y') : $date,
                'irsaliyeNumarasi' => '',
                'irsaliyeTarihi' => '',
                'fisNo' => '',
                'fisTarihi' => '',
                'fisTipi' => '',
                'zRaporNo' => '',
                'okcSeriNo' => '',
                'not' => $invoiceNote,
                'matrah' => round($totalTaxable, 2),
                'malHizmetToplamTutari' => round($totalTaxable, 2),
                'toplamIskonto' => 0,
                'hesaplananKdv' => round($totalKdv, 2),
                'vergilerToplami' => round($totalKdv, 2),
                'vergilerDahilToplamTutar' => $grandTotal,
                'odenecekTutar' => $grandTotal,
                'kalemListesi' => $items,
            ];

            // GİB Portalına Gönder
            $gib = $this->getGibClient();
            
            // Faturayı Oluştur
            $response = $gib->createInvoice($invoiceData);

            // Fatura HTML belgesini çek (Görüntüleme & Çıktı için)
            $html = null;
            try {
                $html = $gib->getHtml($uuid);
            } catch (\Throwable $hEx) {
                Log::warning("GİB Fatura HTML çekilemedi (UUID: {$uuid}): " . $hEx->getMessage());
            }

            $gib->logout();

            // Veritabanını Güncelle
            $order->update([
                'tax_number' => $taxNumber,
                'tax_office' => $taxOffice,
                'company_name' => $companyName,
                'is_invoiced' => true,
                'gib_invoice_uuid' => $uuid,
                'gib_invoice_date' => now(),
                'gib_invoice_status' => 'draft',
                'gib_invoice_html' => $html,
                'gib_invoice_error' => null,
            ]);

            Log::info("GİB E-Arşiv Faturası oluşturuldu. Sipariş No: #{$order->order_number}, UUID: {$uuid}");

            return [
                'success' => true,
                'uuid' => $uuid,
                'message' => 'GİB E-Arşiv Faturası başarıyla oluşturuldu.',
            ];

        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("GİB E-Arşiv Fatura Oluşturma Hatası. Sipariş No: #{$order->order_number}: " . $errorMessage);

            $order->update([
                'gib_invoice_status' => 'failed',
                'gib_invoice_error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'message' => 'Fatura oluşturulurken hata oluştu: ' . $errorMessage,
            ];
        }
    }

    /**
     * GİB Portalından Fatura HTML belgesini çeker
     */
    public function getInvoiceHtml(string $uuid): ?string
    {
        try {
            $gib = $this->getGibClient();
            $html = $gib->getHtml($uuid);
            $gib->logout();

            return $html;
        } catch (\Throwable $e) {
            Log::error("GİB Fatura HTML Alma Hatası (UUID: {$uuid}): " . $e->getMessage());
            return null;
        }
    }
}
