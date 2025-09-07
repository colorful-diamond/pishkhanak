<?php

namespace App\Jobs;

use App\Models\GatewayTransaction;
use App\Services\TelegramNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(GatewayTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info('Processing Telegram notification job', [
                'transaction_id' => $this->transaction->id,
                'transaction_uuid' => $this->transaction->uuid
            ]);

            $telegramService = new TelegramNotificationService();
            $result = $telegramService->sendNewOrderNotification($this->transaction);

            if ($result) {
                Log::info('Telegram notification sent successfully via job', [
                    'transaction_id' => $this->transaction->id
                ]);
            } else {
                Log::warning('Telegram notification failed via job', [
                    'transaction_id' => $this->transaction->id
                ]);
                
                // Throw exception to trigger retry
                throw new \Exception('Failed to send Telegram notification');
            }
        } catch (\Exception $e) {
            Log::error('Error in Telegram notification job', [
                'transaction_id' => $this->transaction->id,
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
    public function failed(\Throwable $exception)
    {
        Log::error('Telegram notification job failed permanently', [
            'transaction_id' => $this->transaction->id,
            'error' => $exception->getMessage()
        ]);
    }
}