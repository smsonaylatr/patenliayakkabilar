<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

use App\Models\Setting;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        [$fromAddress, $fromName] = Setting::getMailSender('order', 'Patenli Ayakkabılar®', 'siparis@patenliayakkabilar.com');

        $defaultSubject = "Siparişiniz Alındı! 🎉 #{$this->order->order_number}";
        $subject = Setting::getMailSubject('order', $defaultSubject, [
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
            view: 'emails.order-confirmation',
        );
    }
}
