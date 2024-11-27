<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // laravel 10
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // laravel 11
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('message.'.$this->message->to_id), // 'message.2'
        ];
    }

    function broadcastWith(): array {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'to_id' => $this->message->to_id,
            // 'attachment' => $this->message->attachment,
            'attachment' => json_decode($this->message->attachment),
            'from_id' => auth()->user()->id
        ];
    }
}
