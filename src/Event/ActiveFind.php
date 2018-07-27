<?php

namespace OkamiChen\TmsAbchina\Event;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ActiveFind
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    protected $data;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function getRaw(){
        return $this->data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('active-find');
    }
}
