<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'en_name',
        'bank_id',
        'logo',
        'card_prefixes',
        'color',
        'is_active',
    ];

    protected $casts = [
        'card_prefixes' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('banks:all');
            \App\Services\BankService::clearCache();
        });

        static::deleted(function () {
            Cache::forget('banks:all');
            \App\Services\BankService::clearCache();
        });
    }

    public static function getAll()
    {
        return Cache::rememberForever('banks:all', function () {
            return self::where('is_active', true)->orderBy('name')->get();
        });
    }
} 