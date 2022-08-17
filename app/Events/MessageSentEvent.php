<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent implements ShouldBroadcastNow
{
    use SerializesModels;
    private $user_id;
    private $room_id;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id,$room_id,$message)
    {
        $this->user_id=$user_id;
        $this->room_id=$room_id;
        $this->data=$message;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('conversation-'.$this->user_id.'-'.$this->room_id);
    }
}
