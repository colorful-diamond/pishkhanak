<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImageGenerationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aiContentId;
    public $sectionIndex;
    public $images;

    /**
     * Create a new event instance.
     */
    public function __construct(int $aiContentId, int $sectionIndex, array $images)
    {
        $this->aiContentId = $aiContentId;
        $this->sectionIndex = $sectionIndex;
        $this->images = $images;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-content.' . $this->aiContentId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'ai_content_id' => $this->aiContentId,
            'section_index' => $this->sectionIndex,
            'images' => $this->images,
            'status' => 'completed'
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'image.generated';
    }
} 