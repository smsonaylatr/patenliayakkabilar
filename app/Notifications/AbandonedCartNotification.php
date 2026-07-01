<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
                    ->subject('Sepetinde ürünler seni bekliyor! 🛒')
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line('Alışverişini tamamlamadığını fark ettik. Sepetindeki harika ürünler hala seni bekliyor.')
                    ->action('Sepetime Git', url('/cart'))
                    ->line('Bizi tercih ettiğin için teşekkür ederiz!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Sepet Hatırlatması',
            'message' => 'Sepetinizdeki ürünler sizi bekliyor.',
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
