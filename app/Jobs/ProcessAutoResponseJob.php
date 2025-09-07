<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\GeminiAutoResponseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessAutoResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Ticket $ticket;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 2;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->onQueue('auto-response');
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiAutoResponseService $autoResponseService): void
    {
        try {
            Log::info('Processing auto-response for ticket', [
                'ticket_id' => $this->ticket->id,
                'ticket_number' => $this->ticket->ticket_number,
                'subject' => $this->ticket->subject
            ]);

            // Process the ticket for auto-response
            $autoResponseResult = $autoResponseService->processTicket($this->ticket);
            
            if ($autoResponseResult && isset($autoResponseResult['success']) && $autoResponseResult['success']) {
                Log::info('Auto-response found for ticket', [
                    'ticket_id' => $this->ticket->id,
                    'confidence' => $autoResponseResult['analysis']['confidence'] ?? 'unknown',
                    'context_id' => $autoResponseResult['analysis']['matched_context_id'] ?? 'unknown'
                ]);
                
                // Apply the auto-response
                $applied = $autoResponseService->applyAutoResponse($this->ticket, $autoResponseResult);
                
                if ($applied) {
                    Log::info('Auto-response successfully applied to ticket', [
                        'ticket_id' => $this->ticket->id,
                        'response_id' => $autoResponseResult['response']->id ?? 'unknown'
                    ]);
                } else {
                    Log::warning('Failed to apply auto-response to ticket', [
                        'ticket_id' => $this->ticket->id
                    ]);
                }
            } else {
                Log::info('No suitable auto-response found for ticket', [
                    'ticket_id' => $this->ticket->id,
                    'analysis' => $autoResponseResult['analysis'] ?? null
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error processing auto-response for ticket', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Auto-response job failed permanently', [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Optionally, you could update the ticket to indicate auto-response failed
        // or send a notification to admins
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'auto-response',
            'ticket:' . $this->ticket->id,
            'user:' . $this->ticket->user_id
        ];
    }
}
