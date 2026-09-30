<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use App\Services\GibEArsivService;
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

        $defaultSubject = "Siparişinizin Faturası - {$this->order->order_number} — Patenli Ayakkabılar®";
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
        return new Content(
            view: 'emails.order-invoice',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        $pdfContent = GibEArsivService::generateInvoicePdf($this->order);

        if (!empty($pdfContent)) {
            $attachments[] = Attachment::fromData(fn () => $pdfContent, "Fatura-{$this->order->order_number}.pdf")
                ->withMime('application/pdf');
            return $attachments;
        }

        if (!empty($this->pdfUrl)) {
            try {
                $attachments[] = Attachment::fromUrl($this->pdfUrl)
                    ->as("Fatura-{$this->order->order_number}.pdf")
                    ->withMime('application/pdf');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("OrderInvoiceMail attachment fromUrl failed: " . $e->getMessage());
            }
        }

        return $attachments;
    }
}
