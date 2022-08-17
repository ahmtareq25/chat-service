<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class RoomMessageSentEvent implements ShouldBroadcastNow
{
    use SerializesModels;
    private $id;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id,$room)
    {
        $this->id=$id;
        $this->data=$room;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('rooms-'.$this->id);
    }
}
