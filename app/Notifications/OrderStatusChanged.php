<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        $prefs = $notifiable->notificationPreference;
        if ($prefs?->email_order_updates ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed and is being prepared.',
            'collecting' => 'A collector is picking up your items from the market.',
            'collected' => 'Your items have been collected and are ready for delivery.',
            'in_transit' => 'Your order is on the way!',
            'delivered' => 'Your order has been delivered. Enjoy!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = $statusMessages[$this->newStatus] ?? "Your order status has been updated to {$this->newStatus}.";

        return (new MailMessage)
            ->subject("Order #{$this->order->order_number} - Status Update")
            ->greeting("Hello {$notifiable->name}!")
            ->line($message)
            ->line("Order Number: {$this->order->order_number}")
            ->line("Total: TZS " . number_format($this->order->total, 2))
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line('Thank you for shopping with Agiza Sokoni!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Order #{$this->order->order_number} status changed to {$this->newStatus}",
        ];
    }
}
