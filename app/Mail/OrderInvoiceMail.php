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

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $pdfUrl;

    public function __construct(Order $order, string $pdfUrl)
    {
        $this->order = $order;
        $this->pdfUrl = $pdfUrl;
    }

    public function envelope(): Envelope
    {
        [$fromAddress, $fromName] = Setting::getMailSender('invoice', 'Patenli Ayakkabılar®', 'siparis@patenliayakkabilar.com');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: "Siparişinizin Faturası - {$this->order->order_number} — Patenli Ayakkabılar®",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->pdfUrl)) {
            // Attach PDF from URL
            $attachments[] = Attachment::fromUrl($this->pdfUrl)
                ->as("Fatura-{$this->order->order_number}.pdf")
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
