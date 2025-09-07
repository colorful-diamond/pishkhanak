<?php

namespace App\Models;

use Bavix\Wallet\Models\Transaction as BavixTransaction;

class Transaction extends BavixTransaction
{
    /**
     * Get transaction type in Persian
     */
    public function getTypeText(): string
    {
        return match($this->type) {
            'deposit' => 'واریز',
            'withdraw' => 'برداشت',
            default => 'نامشخص'
        };
    }

    /**
     * Get status color classes based on transaction confirmation and type
     */
    public function getStatusColor(): string
    {
        if (!$this->confirmed) {
            return 'bg-yellow-500'; // Pending/unconfirmed
        }

        return match($this->type) {
            'deposit' => 'bg-green-500', // Success/deposit
            'withdraw' => 'bg-blue-500', // Withdrawal
            default => 'bg-gray-500'     // Default
        };
    }

    /**
     * Get status badge classes (background + text) for status badges
     */
    public function getStatusBadgeClass(): string
    {
        if (!$this->confirmed) {
            return 'bg-yellow-100 text-yellow-800'; // Pending/unconfirmed
        }

        return match($this->type) {
            'deposit' => 'bg-green-100 text-green-800', // Success/deposit
            'withdraw' => 'bg-blue-100 text-blue-800', // Withdrawal
            default => 'bg-gray-100 text-gray-800'     // Default
        };
    }

    /**
     * Get status text in Persian
     */
    public function getStatusText(): string
    {
        if (!$this->confirmed) {
            return 'در انتظار تایید';
        }

        return match($this->type) {
            'deposit' => 'واریز موفق',
            'withdraw' => 'برداشت موفق',
            default => 'تکمیل شده'
        };
    }

    /**
     * Get gateway name from meta data
     */
    public function getGatewayAttribute()
    {
        $meta = $this->meta ?? [];
        $gatewayName = $meta['gateway_name'] ?? null;
        
        if ($gatewayName) {
            return (object) ['name' => $gatewayName];
        }

        return null;
    }

    /**
     * Get transaction ID for display
     */
    public function getTransactionIdAttribute(): string
    {
        return $this->uuid ?? $this->id;
    }

    /**
     * Get formatted amount for display
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount) . ' تومان';
    }

    /**
     * Get transaction description from meta
     */
    public function getDescriptionAttribute(): ?string
    {
        $meta = $this->meta ?? [];
        return $meta['description'] ?? null;
    }
} 