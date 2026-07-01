<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartRecoveryCouponNotification extends Notification
{
    use Queueable;

    public $couponCode;

    /**
     * Create a new notification instance.
     */
    public function __construct($couponCode)
    {
        $this->couponCode = $couponCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Sana Özel %10 İndirim Fırsatı! 🎁')
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line('Sepetinde unuttuğun ürünler seni bekliyor. Alışverişini tamamlaman için sana özel %10 indirim kuponu tanımladık!')
                    ->line('Kupon Kodun: **' . $this->couponCode . '**')
                    ->action('Sepetine Git ve İndirimi Kullan', url('/cart'))
                    ->line('Not: Kupon kodu 48 saat geçerlidir.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '%10 İndirim Kazandınız',
            'message' => 'Sepetinizdeki ürünleri almak için %10 indirim kuponunuz: ' . $this->couponCode,
            'action_url' => url('/cart'),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
