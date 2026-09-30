<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockBackMail extends Mailable
{
    use Queueable, SerializesModels;

    public Product $product;
    public ?ProductVariant $variant;

    public function __construct(Product $product, ?ProductVariant $variant = null)
    {
        $this->product = $product;
        $this->variant = $variant;
    }

    public function envelope(): Envelope
    {
        $sizeText = $this->variant ? " ({$this->variant->size} Beden)" : '';
        [$fromAddress, $fromName] = \App\Models\Setting::getMailSender('stock', 'Patenli Ayakkabılar®', 'info@patenliayakkabilar.com');

        $defaultSubject = "🎉 Müjde! Beklediğiniz {$this->product->name}{$sizeText} Stokta!";
        $subject = \App\Models\Setting::getMailSubject('stock', $defaultSubject, [
            '{urun_adi}' => $this->product->name,
            '{product_name}' => $this->product->name,
            '{beden}' => $this->variant ? "({$this->variant->size} Beden)" : '',
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
            view: 'emails.stock_back',
        );
    }
}
