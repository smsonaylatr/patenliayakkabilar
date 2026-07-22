<?php

namespace App\Services;

use App\Models\Order;
use Mlevent\Fatura\Gib;
use Mlevent\Fatura\Enums\Unit;
use Mlevent\Fatura\Enums\Currency;
use Mlevent\Fatura\Enums\InvoiceType;
use Mlevent\Fatura\Models\InvoiceModel;
use Mlevent\Fatura\Models\InvoiceItemModel;
use Exception;
use Illuminate\Support\Facades\Log;

class GibInvoiceService
{
    protected Gib $gib;

    public function __construct()
    {
        $settings = \App\Models\Setting::whereIn('key', ['gib_username', 'gib_password'])->pluck('value', 'key')->toArray();
        $username = $settings['gib_username'] ?? '';
        $password = $settings['gib_password'] ?? '';

        if (empty($username) || empty($password)) {
            throw new Exception("GİB kullanıcı adı ve şifresi yapılandırılmamış.");
        }

        $this->gib = new Gib();
        $this->gib->login($username, $password);
    }

    /**
     * Creates a draft invoice in GİB portal
     */
    public function createDraftFromOrder(Order $order): ?string
    {
        try {
            // Split name into first and last name
            $nameParts = explode(' ', trim($order->customer_name));
            $lastName = array_pop($nameParts);
            $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : $lastName;
            if (empty($firstName)) {
                $firstName = $lastName;
            }

            // GİB portalında VKN zorunludur. Gerçek kişi için TC, bilinmiyorsa 11111111111 kullanılır
            $vkn = '11111111111';

            $invoice = new InvoiceModel(
                paraBirimi: Currency::TRY,
                faturaTipi: InvoiceType::Satis,
                vknTckn: $vkn,
                aliciAdi: $firstName,
                aliciSoyadi: $lastName,
                mahalleSemtIlce: $order->shipping_district ?? 'Bilinmeyen İlçe',
                sehir: $order->shipping_city ?? 'Bilinmeyen Şehir',
                ulke: 'Türkiye',
                adres: $order->shipping_address ?? 'Adres belirtilmemiş',
                tel: $order->customer_phone ?? '',
                eposta: $order->customer_email ?? '',
                not: "Sipariş No: {$order->order_number}"
            );

            // Fetch actual order items
            $items = $order->items;
            
            if ($items && $items->count() > 0) {
                foreach ($items as $item) {
                    $priceWithoutVat = round($item->price / 1.20, 2);
                    $invoice->addItem(
                        new InvoiceItemModel(
                            malHizmet: $item->product_name ?? 'Ürün',
                            miktar: $item->quantity ?? 1,
                            birim: Unit::Adet,
                            birimFiyat: $priceWithoutVat,
                            kdvOrani: 20
                        )
                    );
                }
                
                // Add shipping as an item if exists
                if ($order->shipping_price > 0) {
                    $invoice->addItem(
                        new InvoiceItemModel(
                            malHizmet: 'Kargo Ücreti',
                            miktar: 1,
                            birim: Unit::Adet,
                            birimFiyat: round($order->shipping_price / 1.20, 2),
                            kdvOrani: 20
                        )
                    );
                }
            } else {
                $priceWithoutVat = round($order->grand_total / 1.20, 2);
                $invoice->addItem(
                    new InvoiceItemModel(
                        malHizmet: 'Muhtelif Ayakkabı ve Paten Ürünleri',
                        miktar: 1,
                        birim: Unit::Adet,
                        birimFiyat: $priceWithoutVat,
                        kdvOrani: 20
                    )
                );
            }

            if ($this->gib->createDraft($invoice)) {
                return $invoice->getUuid();
            }

            throw new Exception("Fatura taslağı oluşturulamadı.");
        } catch (Exception $e) {
            Log::error("GİB Fatura Taslağı Oluşturma Hatası: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Start SMS verification process
     */
    public function startSmsVerification(): string
    {
        return $this->gib->startSmsVerification();
    }

    /**
     * Complete SMS verification to sign the invoice
     */
    public function completeSmsVerification(string $smsCode, string $operationId, array $uuids): bool
    {
        return $this->gib->completeSmsVerification($smsCode, $operationId, $uuids);
    }

    /**
     * Get HTML content of the invoice
     */
    public function getHtml(string $uuid): string
    {
        return $this->gib->getHtml($uuid);
    }

    /**
     * Safely logout
     */
    public function logout(): void
    {
        $this->gib->logout();
    }
}
