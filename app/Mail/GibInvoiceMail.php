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
        $settings = Setting::whereIn('key', ['smtp_from_address', 'smtp_from_name'])->pluck('value', 'key')->toArray();
        $fromAddress = $settings['smtp_from_address'] ?? config('mail.from.address', 'destek@patenliayakkabilar.com');
        $fromName = $settings['smtp_from_name'] ?? config('mail.from.name', 'Patenli Ayakkabılar');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: "Siparişinizin E-Arşiv Faturası (#{$this->order->order_number}) - Patenli Ayakkabılar",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gib-invoice',
            with: [
                'order' => $this->order,
                'invoiceUrl' => $this->invoiceUrl,
                'logoUrl' => $this->logoUrl,
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
