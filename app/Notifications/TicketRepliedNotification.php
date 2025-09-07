<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Ticket $ticket;
    protected TicketMessage $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketMessage $message)
    {
        $this->ticket = $ticket;
        $this->message = $message;
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
        $isFromSupport = $this->message->isFromSupport();
        $replierName = $this->message->user->name;
        
        return (new MailMessage)
            ->subject('پاسخ جدید به تیکت - ' . $this->ticket->ticket_number)
            ->greeting('سلام ' . $notifiable->name)
            ->line($isFromSupport ? 'تیم پشتیبانی به تیکت شما پاسخ داده است:' : 'پاسخ جدیدی به تیکت دریافت شد:')
            ->line('**شماره تیکت:** ' . $this->ticket->ticket_number)
            ->line('**موضوع:** ' . $this->ticket->subject)
            ->line('**پاسخ دهنده:** ' . $replierName)
            ->line('**پیام:**')
            ->line(Str::limit($this->message->message, 200))
            ->action(
                $isFromSupport ? 'مشاهده پاسخ' : 'مشاهده تیکت',
                $isFromSupport ? 
                    route('app.user.tickets.show', $this->ticket->ticket_hash) : 
                    url('/access/tickets')
            )
            ->line($isFromSupport ? 
                'برای ادامه گفتگو، روی لینک بالا کلیک کنید.' : 
                'لطفاً به این پاسخ توجه کنید و در صورت نیاز اقدام کنید.'
            )
            ->salutation('با تشکر، تیم پشتیبانی ' . config('app.name'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $isFromSupport = $this->message->isFromSupport();
        $replierName = $this->message->user->name;
        
        return "پاسخ جدید به تیکت {$this->ticket->ticket_number}\n" .
               "از: {$replierName}\n" .
               "پیام: " . Str::limit($this->message->message, 100) . "\n" .
               ($isFromSupport ? "برای مشاهده به سایت مراجعه کنید." : "برای مشاهده به پنل ادمین مراجعه کنید.");
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $isFromSupport = $this->message->isFromSupport();
        
        return [
            'type' => 'ticket_replied',
            'ticket_id' => $this->ticket->id,
            'message_id' => $this->message->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'replier_name' => $this->message->user->name,
            'is_from_support' => $isFromSupport,
            'message_preview' => Str::limit($this->message->message, 100),
            'replied_at' => $this->message->created_at,
            'url' => $isFromSupport ? 
                route('app.user.tickets.show', $this->ticket->ticket_hash) : 
                '/access/tickets',
            'message' => $isFromSupport ? 
                "تیم پشتیبانی به تیکت #{$this->ticket->ticket_number} پاسخ داد" :
                "پاسخ جدید به تیکت #{$this->ticket->ticket_number} توسط {$this->message->user->name}",
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