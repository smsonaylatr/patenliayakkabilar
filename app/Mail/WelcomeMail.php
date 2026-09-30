<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public \App\Models\User $user;

    public function __construct(\App\Models\User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        [$fromAddress, $fromName] = \App\Models\Setting::getMailSender('welcome', 'Patenli Ayakkabılar®', 'siparis@patenliayakkabilar.com');

        $defaultSubject = 'Patenli Ayakkabılar® — Aramıza Hoş Geldiniz';
        $subject = \App\Models\Setting::getMailSubject('welcome', $defaultSubject, [
            '{musteri_adi}' => $this->user->name ?: 'Değerli Müşterimiz',
            '{name}' => $this->user->name ?: 'Değerli Müşterimiz',
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
            view: 'emails.welcome',
        );
    }
}
