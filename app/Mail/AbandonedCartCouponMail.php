<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartCouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cart;
    public $couponCode;
    public $customerName;
    public $expiresAt;

    /**
     * %10 kuponlu sepet hatırlatma maili.
     */
    public function __construct($cart, string $couponCode, string $expiresAt)
    {
        $this->cart = $cart;
        $this->couponCode = $couponCode;
        $this->customerName = $cart->user?->name ?? $cart->guest_name ?? 'Değerli Müşterimiz';
        $this->expiresAt = $expiresAt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sana Özel %10 İndirim! Sepetindeki Ürünleri Kaçırma 🎁',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.abandoned_cart_coupon',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
