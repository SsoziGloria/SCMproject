<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification
{
    use Queueable;

    /**
     * The low stock items.
     *
     * @var mixed
     */
    protected $lowStockItems;

    /**
     * Create a new notification instance.
     */
    public function __construct($lowStockItems)
    {
        $this->lowStockItems = $lowStockItems;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via( $notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Inventory Alert')
            ->line('Some inventory items are low or near expiration.');

        foreach ($this->lowStockItems as $item) {
            $message->line($item->product_name . ' (Quantity: ' . $item->quantity . ')');
        }

        return $message;
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'low_stock_items' => collect($this->lowStockItems)->map(function($item) {
            return [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
            ];
        })->toArray(),
        'message' => 'Some inventory items are low or near expiration.',
        ];
    }
}
