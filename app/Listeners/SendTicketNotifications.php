<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Events\TicketReplied;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketRepliedNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendTicketNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle ticket created events.
     */
    public function handleTicketCreated(TicketCreated $event): void
    {
        try {
            $ticket = $event->ticket;
            
            // Get all admin users who should be notified
            $adminUsers = User::role(['admin', 'support'])->get();
            
            if ($adminUsers->isEmpty()) {
                Log::warning('No admin users found to notify for ticket creation', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number
                ]);
                return;
            }
            
            // Send notifications to all admins
            foreach ($adminUsers as $admin) {
                $admin->notify(new TicketCreatedNotification($ticket));
            }
            
            Log::info('Ticket creation notifications sent', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'notified_admins' => $adminUsers->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send ticket creation notifications', [
                'ticket_id' => $event->ticket->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle ticket replied events.
     */
    public function handleTicketReplied(TicketReplied $event): void
    {
        try {
            $ticket = $event->ticket;
            $message = $event->message;
            
            // Determine who should be notified based on who replied
            $isFromSupport = $message->isFromSupport();
            
            if ($isFromSupport) {
                // Support replied - notify the ticket owner (user)
                $ticket->user->notify(new TicketRepliedNotification($ticket, $message));
                
                Log::info('Support reply notification sent to user', [
                    'ticket_id' => $ticket->id,
                    'message_id' => $message->id,
                    'user_id' => $ticket->user->id
                ]);
                
            } else {
                // User replied - notify admins and assigned agent
                $usersToNotify = collect();
                
                // Add assigned agent if exists
                if ($ticket->assignedTo) {
                    $usersToNotify->push($ticket->assignedTo);
                }
                
                // Add other admin users
                $adminUsers = User::role(['admin', 'support'])
                    ->where('id', '!=', $ticket->assigned_to ?? 0)
                    ->get();
                    
                $usersToNotify = $usersToNotify->merge($adminUsers)->unique('id');
                
                if ($usersToNotify->isEmpty()) {
                    Log::warning('No admin users found to notify for ticket reply', [
                        'ticket_id' => $ticket->id,
                        'message_id' => $message->id
                    ]);
                    return;
                }
                
                // Send notifications
                foreach ($usersToNotify as $user) {
                    $user->notify(new TicketRepliedNotification($ticket, $message));
                }
                
                Log::info('User reply notifications sent to admins', [
                    'ticket_id' => $ticket->id,
                    'message_id' => $message->id,
                    'notified_users' => $usersToNotify->count()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to send ticket reply notifications', [
                'ticket_id' => $event->ticket->id ?? null,
                'message_id' => $event->message->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            TicketCreated::class => 'handleTicketCreated',
            TicketReplied::class => 'handleTicketReplied',
        ];
    }
} 