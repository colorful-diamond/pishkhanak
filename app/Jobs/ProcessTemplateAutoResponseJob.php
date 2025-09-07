<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TemplateAutoResponseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTemplateAutoResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Ticket $ticket;
    public int $timeout = 60;
    public int $tries = 2;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->onQueue('auto-response');
    }

    public function handle(TemplateAutoResponseService $service): void
    {
        try {
            // Skip if already has auto-response
            if ($this->ticket->is_auto_responded) {
                return;
            }

            Log::info('Processing template auto-response', [
                'ticket_id' => $this->ticket->id,
                'subject' => $this->ticket->subject
            ]);

            $result = $service->processTicket($this->ticket);

            if ($result) {
                Log::info('Template auto-response applied', [
                    'ticket_id' => $this->ticket->id,
                    'template' => $result['title']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Template auto-response job failed', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}