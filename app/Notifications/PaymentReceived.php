<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

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
        return (new MailMessage)
            ->subject("Payment Received - Order #{$this->payment->order->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line('We have received your payment.')
            ->line("Amount: TZS " . number_format($this->payment->amount, 2))
            ->line("Payment Method: " . ucfirst($this->payment->payment_method))
            ->line("Transaction ID: {$this->payment->transaction_id}")
            ->action('View Order', url("/orders/{$this->payment->order_id}"))
            ->line('Thank you for your payment!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'order_id' => $this->payment->order_id,
            'amount' => $this->payment->amount,
            'message' => "Payment of TZS " . number_format($this->payment->amount, 2) . " received",
        ];
    }
}
