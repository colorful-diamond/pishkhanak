<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'ticket_message_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
    ];

    /**
     * Get the ticket that owns the attachment
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the message that owns the attachment
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeAttribute($value): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $value;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get file icon based on mime type
     */
    public function getFileIcon(): string
    {
        $mimeType = $this->mime_type;
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (str_starts_with($mimeType, 'application/pdf')) {
            return 'pdf';
        } elseif (str_starts_with($mimeType, 'text/')) {
            return 'document';
        } else {
            return 'file';
        }
    }

    /**
     * Get download URL
     */
    public function getDownloadUrl(): string
    {
        return route('app.user.tickets.attachment.download', $this->id);
    }
} 