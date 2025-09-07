<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class TicketTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'bg_color',
        'emoji',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
            
            // Set default background color if not provided
            if (empty($tag->bg_color)) {
                $tag->bg_color = $tag->getLightColor($tag->color);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get tickets associated with this tag
     */
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tag_pivot', 'ticket_tag_id', 'ticket_id')
                    ->withPivot('created_at');
    }

    /**
     * Scope to get only active tags
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get predefined color options
     */
    public static function getColorOptions(): array
    {
        return [
            ['name' => 'PERSIAN_TEXT_4330449c', 'color' => '#EF4444', 'bg_color' => '#FEE2E2', 'emoji' => '🔴'],
            ['name' => 'PERSIAN_TEXT_8eadfdd5', 'color' => '#F97316', 'bg_color' => '#FED7AA', 'emoji' => '🟠'],
            ['name' => 'PERSIAN_TEXT_3070d7b4', 'color' => '#F59E0B', 'bg_color' => '#FEF3C7', 'emoji' => '🟡'],
            ['name' => 'PERSIAN_TEXT_7948ec1e', 'color' => '#10B981', 'bg_color' => '#D1FAE5', 'emoji' => '🟢'],
            ['name' => 'PERSIAN_TEXT_79f8323e', 'color' => '#3B82F6', 'bg_color' => '#DBEAFE', 'emoji' => '🔵'],
            ['name' => 'PERSIAN_TEXT_32e682f1', 'color' => '#8B5CF6', 'bg_color' => '#EDE9FE', 'emoji' => '🟣'],
            ['name' => 'PERSIAN_TEXT_cdc642e3', 'color' => '#EC4899', 'bg_color' => '#FCE7F3', 'emoji' => '🩷'],
            ['name' => 'PERSIAN_TEXT_c8f96b49', 'color' => '#6B7280', 'bg_color' => '#F3F4F6', 'emoji' => '⚫'],
            ['name' => 'PERSIAN_TEXT_30e597d0', 'color' => '#06B6D4', 'bg_color' => '#CFFAFE', 'emoji' => '🟦'],
            ['name' => 'PERSIAN_TEXT_4866d164', 'color' => '#6366F1', 'bg_color' => '#E0E7FF', 'emoji' => '🔷'],
        ];
    }

    /**
     * Get a lighter version of the color for background
     */
    protected function getLightColor($color): string
    {
        // Convert hex to RGB
        $hex = str_replace('#', '', $color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Create a lighter version (blend with white)
        $r = min(255, $r + (255 - $r) * 0.85);
        $g = min(255, $g + (255 - $g) * 0.85);
        $b = min(255, $b + (255 - $b) * 0.85);
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Get tag display for Telegram
     */
    public function getTelegramDisplay(): string
    {
        $emoji = $this->emoji ?: '🏷️';
        return "{$emoji} {$this->name}";
    }

    /**
     * Get HTML display for web
     */
    public function getHtmlDisplay(): string
    {
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="color: %s; background-color: %s;">%s</span>',
            $this->color,
            $this->bg_color,
            e($this->name)
        );
    }
}