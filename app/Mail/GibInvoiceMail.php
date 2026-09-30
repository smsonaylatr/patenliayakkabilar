<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class GibInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $invoiceUrl;
    public string $logoUrl;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->invoiceUrl = route('orders.gib-invoice', $order);
        
        $customLogo = Setting::where('key', 'gib_logo_url')->value('value');
        $this->logoUrl = $customLogo ?: asset('favicon.png');
    }

    public function envelope(): Envelope
    {
        [$fromAddress, $fromName] = Setting::getMailSender('invoice', 'Patenli Ayakkabılar®', 'siparis@patenliayakkabilar.com');

        $defaultSubject = "Siparişinizin E-Arşiv Faturası (#{$this->order->order_number}) - Patenli Ayakkabılar®";
        $subject = Setting::getMailSubject('invoice', $defaultSubject, [
            '{siparis_no}' => $this->order->order_number,
            '{order_number}' => $this->order->order_number,
            '{order_no}' => $this->order->order_number,
            '{musteri_adi}' => $this->order->customer_name ?: 'Değerli Müşterimiz',
            '{toplam_tutar}' => number_format($this->order->grand_total ?? $this->order->total_amount ?? 0, 2, ',', '.') . ' ₺',
            '{magaza_adi}' => 'Patenli Ayakkabılar®',
        ]);

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $defaultSubject = "Siparişinizin E-Arşiv Faturası (#{$this->order->order_number}) - Patenli Ayakkabılar®";
        $subject = Setting::getMailSubject('invoice', $defaultSubject, [
            '{siparis_no}' => $this->order->order_number,
            '{order_number}' => $this->order->order_number,
            '{order_no}' => $this->order->order_number,
            '{musteri_adi}' => $this->order->customer_name ?: 'Değerli Müşterimiz',
            '{toplam_tutar}' => number_format($this->order->grand_total ?? $this->order->total_amount ?? 0, 2, ',', '.') . ' ₺',
            '{magaza_adi}' => 'Patenli Ayakkabılar®',
        ]);

        return new Content(
            view: 'emails.gib-invoice',
            with: [
                'order' => $this->order,
                'invoiceUrl' => $this->invoiceUrl,
                'logoUrl' => $this->logoUrl,
                'mailSubject' => $subject,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->order->gib_invoice_html)) {
            $fileName = "E-Arsiv-Fatura-{$this->order->order_number}.html";
            $attachments[] = Attachment::fromData(fn () => $this->order->gib_invoice_html, $fileName)
                ->withMime('text/html');
        }

        return $attachments;
    }
}
