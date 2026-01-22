<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $messageId;
    public $roomId;

    public function __construct($messageId, $roomId)
    {
        $this->messageId = $messageId;
        $this->roomId = $roomId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'message.deleted';
    }
}
