<?php

namespace App\Listeners;

use App\Models\MailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Events\Dispatcher;

class MailEventSubscriber
{
    /**
     * Mail gönderilmeye başladığında tetiklenir (log durumunu 'sending' yapar)
     */
    public function handleSending(MessageSending $event): void
    {
        try {
            $message = $event->message;
            $toAddresses = $message->getTo();
            
            $toEmail = '';
            $toName = null;
            
            // İlk alıcıyı alıyoruz
            if (!empty($toAddresses)) {
                foreach ($toAddresses as $address) {
                    $toEmail = $address->getAddress();
                    $toName = $address->getName();
                    break;
                }
            }
            
            // Mailable sınıf adını belirle
            $mailableClass = $event->data['__mailable'] ?? get_class($event->message);
            
            MailLog::create([
                'mailable_class' => $mailableClass,
                'to_email' => $toEmail,
                'to_name' => $toName ?: null,
                'subject' => $message->getSubject(),
                'status' => 'sending',
            ]);
        } catch (\Throwable $e) {
            // Loglama hatası mail gönderimini durdurmasın
            report($e);
        }
    }

    /**
     * Mail başarıyla gönderildiğinde tetiklenir (log durumunu 'sent' yapar)
     */
    public function handleSent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $toAddresses = $message->getTo();
            
            $toEmail = '';
            if (!empty($toAddresses)) {
                foreach ($toAddresses as $address) {
                    $toEmail = $address->getAddress();
                    break;
                }
            }

            // İlgili e-posta adresine ait en son 'sending' durumundaki logu bul ve güncelle
            if ($toEmail) {
                $log = MailLog::where('to_email', $toEmail)
                              ->where('status', 'sending')
                              ->latest('id')
                              ->first();

                if ($log) {
                    $log->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Subscriber'ın dinleyeceği event'leri kaydet
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            MessageSending::class => 'handleSending',
            MessageSent::class => 'handleSent',
        ];
    }
}
