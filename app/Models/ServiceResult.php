<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Hekmatinasser\Verta\Verta;

class ServiceResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'user_id',
        'wallet_transaction_id',
        'result_hash',
        'input_data',
        'output_data',
        'status',
        'error_message',
        'processed_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Boot the model and generate hash on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            \Illuminate\Support\Facades\Log::info('ServiceResult creating event triggered', [
                'model_exists' => $model ? 'yes' : 'no',
                'service_id' => $model->service_id ?? 'null',
                'user_id' => $model->user_id ?? 'null',
                'current_hash' => $model->result_hash ?? 'null',
                'status' => $model->status ?? 'null',
            ]);

            if (empty($model->result_hash)) {
                $generatedHash = self::generateHash();
                $model->result_hash = $generatedHash;
                
                // Log hash generation for debugging
                \Illuminate\Support\Facades\Log::info('ServiceResult hash generated during creation', [
                    'generated_hash' => $generatedHash,
                    'hash_length' => strlen($generatedHash),
                    'service_id' => $model->service_id,
                    'user_id' => $model->user_id,
                    'model_attributes' => $model->getAttributes(),
                ]);
            } else {
                // Log if hash was already set
                \Illuminate\Support\Facades\Log::info('ServiceResult hash already exists during creation', [
                    'existing_hash' => $model->result_hash,
                    'service_id' => $model->service_id,
                    'user_id' => $model->user_id,
                ]);
            }
        });

        static::created(function ($model) {
            // Log after successful creation to verify hash was saved
            \Illuminate\Support\Facades\Log::info('ServiceResult created event triggered', [
                'id' => $model->id,
                'result_hash' => $model->result_hash,
                'service_id' => $model->service_id,
                'user_id' => $model->user_id,
                'status' => $model->status,
                'created_at' => $model->created_at,
                'all_attributes' => $model->getAttributes(),
            ]);

            // Immediate verification that the record actually exists in database
            $verifyRecord = self::find($model->id);
            \Illuminate\Support\Facades\Log::info('ServiceResult immediate verification after creation', [
                'original_id' => $model->id,
                'original_hash' => $model->result_hash,
                'verification_found' => $verifyRecord ? 'yes' : 'no',
                'verification_id' => $verifyRecord->id ?? 'null',
                'verification_hash' => $verifyRecord->result_hash ?? 'null',
            ]);
        });

        static::saved(function ($model) {
            \Illuminate\Support\Facades\Log::info('ServiceResult saved event triggered', [
                'id' => $model->id,
                'result_hash' => $model->result_hash,
                'was_recently_created' => $model->wasRecentlyCreated,
                'is_dirty' => $model->isDirty(),
                'changes' => $model->getChanges(),
            ]);
        });
    }

    /**
     * Generate a unique 16-character hash
     *
     * @return string
     */
    public static function generateHash(): string
    {
        do {
            $hash = Str::random(16);
        } while (static::where('result_hash', $hash)->exists());

        return $hash;
    }

    /**
     * Get the service that owns the result
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user that owns the result
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet transaction associated with this result
     */
    public function walletTransaction()
    {
        return $this->belongsTo(\Bavix\Wallet\Models\Transaction::class, 'wallet_transaction_id');
    }

    /**
     * Scope a query to only include recent results
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('processed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include successful results
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed results
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get the formatted result data for display
     *
     * @return array
     */
    public function getFormattedResult(): array
    {
        $result = $this->output_data ?? [];
        
        // Add processing information
        $result['processed_at'] = Verta::instance($this->processed_at)->format('Y/n/j H:i:s');
        $result['result_id'] = $this->result_hash;
        
        return $result;
    }

    /**
     * Check if the result is expired (older than 30 days)
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->processed_at->diffInDays(now()) > 30;
    }
} 