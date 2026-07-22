<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ParasutInvoiceService
{
    protected string $companyId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;
    protected string $baseUrl = 'https://api.parasut.com';

    public function __construct()
    {
        $settings = Setting::whereIn('key', [
            'parasut_company_id',
            'parasut_client_id',
            'parasut_client_secret',
            'parasut_username',
            'parasut_password',
        ])->pluck('value', 'key')->toArray();

        $this->companyId = $settings['parasut_company_id'] ?? '';
        $this->clientId = $settings['parasut_client_id'] ?? '';
        $this->clientSecret = $settings['parasut_client_secret'] ?? '';
        $this->username = $settings['parasut_username'] ?? '';
        $this->password = $settings['parasut_password'] ?? '';

        if (empty($this->companyId) || empty($this->clientId)) {
            throw new Exception("Paraşüt API ayarları yapılandırılmamış.");
        }
    }

    /**
     * Authenticate and get Access Token
     */
    protected function getToken(): string
    {
        $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => 'password',
            'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob'
        ]);

        if ($response->failed()) {
            throw new Exception("Paraşüt API kimlik doğrulaması başarısız: " . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Issue an invoice for an order and return the PDF download link
     */
    public function createInvoiceFromOrder(Order $order): string
    {
        $token = $this->getToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // 1. Create or Find Contact
        $contactId = $this->createContact($order, $headers);

        // 2. Create Sales Invoice
        $invoiceId = $this->createSalesInvoice($order, $contactId, $headers);

        // 3. Convert to E-Archive/E-Invoice
        // In Turkey B2C (without explicit TC) is E-Archive. We assume B2C.
        $this->createEArchive($invoiceId, $headers);

        // 4. Return the PDF URL
        return $this->getInvoicePdfUrl($invoiceId, $headers);
    }

    protected function createContact(Order $order, array $headers): string
    {
        $data = [
            'data' => [
                'type' => 'contacts',
                'attributes' => [
                    'name' => $order->customer_name,
                    'contact_type' => 'person',
                    'tax_number' => '11111111111',
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'district' => $order->shipping_district,
                    'country' => 'Türkiye'
                ]
            ]
        ];

        $response = Http::withHeaders($headers)
            ->post("{$this->baseUrl}/v4/{$this->companyId}/contacts", $data);

        if ($response->failed()) {
            throw new Exception("Müşteri oluşturulamadı: " . $response->body());
        }

        return $response->json('data.id');
    }

    protected function createSalesInvoice(Order $order, string $contactId, array $headers): string
    {
        $details = [];
        
        $priceWithoutVat = round($order->grand_total / 1.20, 2);
        $details[] = [
            'type' => 'sales_invoice_details',
            'attributes' => [
                'quantity' => 1,
                'unit_price' => $priceWithoutVat,
                'vat_rate' => 20,
                'description' => 'Muhtelif Ayakkabı ve Paten Ürünleri'
            ]
        ];

        $data = [
            'data' => [
                'type' => 'sales_invoices',
                'attributes' => [
                    'item_type' => 'invoice',
                    'description' => "Sipariş No: {$order->order_number}",
                    'issue_date' => date('Y-m-d'),
                    'due_date' => date('Y-m-d'),
                ],
                'relationships' => [
                    'details' => [
                        'data' => $details
                    ],
                    'contact' => [
                        'data' => [
                            'type' => 'contacts',
                            'id' => $contactId
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders($headers)
            ->post("{$this->baseUrl}/v4/{$this->companyId}/sales_invoices", $data);

        if ($response->failed()) {
            throw new Exception("Fatura oluşturulamadı: " . $response->body());
        }

        return $response->json('data.id');
    }

    protected function createEArchive(string $invoiceId, array $headers): void
    {
        $data = [
            'data' => [
                'type' => 'e_archives',
                'relationships' => [
                    'sales_invoice' => [
                        'data' => [
                            'type' => 'sales_invoices',
                            'id' => $invoiceId
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders($headers)
            ->post("{$this->baseUrl}/v4/{$this->companyId}/e_archives", $data);

        if ($response->failed()) {
            throw new Exception("E-Arşiv faturası resmileştirilemedi: " . $response->body());
        }
    }

    protected function getInvoicePdfUrl(string $invoiceId, array $headers): string
    {
        // Give Paraşüt a few seconds to generate the PDF internally
        sleep(2);
        
        $response = Http::withHeaders($headers)
            ->get("{$this->baseUrl}/v4/{$this->companyId}/e_archives?filter[sales_invoice_id]={$invoiceId}");

        if ($response->failed()) {
            return '';
        }

        $items = $response->json('data');
        if (!empty($items) && isset($items[0]['attributes']['pdf_url'])) {
            return $items[0]['attributes']['pdf_url'];
        }

        return '';
    }
}
