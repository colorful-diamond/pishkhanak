<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class CustomUserEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $hash;
    public $title;
    public $value;
    public $type;
    public $options;

    public function __construct($hash , $title, $value , $type , $options = null)
    {
        $this->hash = $hash;
        $this->title = $title;
        $this->value = $value;
        $this->type = $type;
        $this->options = $options;
    }

    public function broadcastOn() : array
    {
        return [
            new PrivateChannel('user.' . auth()->id()),
            new Channel('custom-event.' . $this->hash),
        ];
    }
}
