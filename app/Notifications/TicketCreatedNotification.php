<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Str;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Ticket $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add email if user has email
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        // Add SMS if user has mobile
        if ($notifiable->mobile) {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تیکت جدید ایجاد شد - ' . $this->ticket->ticket_number)
            ->greeting('سلام ' . $notifiable->name)
            ->line('تیکت جدیدی با مشخصات زیر ایجاد شد:')
            ->line('**شماره تیکت:** ' . $this->ticket->ticket_number)
            ->line('**موضوع:** ' . $this->ticket->subject)
            ->line('**اولویت:** ' . $this->ticket->getPriorityText())
            ->line('**دسته‌بندی:** ' . $this->ticket->getCategoryText())
            ->line('**کاربر:** ' . $this->ticket->user->name)
            ->line('**توضیحات:** ' . Str::limit($this->ticket->description, 150))
                            ->action('مشاهده تیکت', url('/access/tickets'))
            ->line('لطفاً در اسرع وقت به این تیکت پاسخ دهید.')
            ->salutation('با تشکر، تیم پشتیبانی ' . config('app.name'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return "تیکت جدید: {$this->ticket->ticket_number}\n" .
               "موضوع: {$this->ticket->subject}\n" .
               "کاربر: {$this->ticket->user->name}\n" .
               "اولویت: {$this->ticket->getPriorityText()}\n" .
               "برای مشاهده به پنل ادمین مراجعه کنید.";
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'ticket_created',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'user_name' => $this->ticket->user->name,
            'created_at' => $this->ticket->created_at,
            'url' => '/access/tickets',
            'message' => "تیکت جدید #{$this->ticket->ticket_number} توسط {$this->ticket->user->name} ایجاد شد",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
} 