<?php

namespace OkamiChen\TmsAbchina\Listener;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OkamiChen\TmsAbchina\Event\ActiveFind;

class ActiveFindListener
{
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(ActiveFind $event)
    {
        //$this->setCache($event->getRaw());
    }
    
    protected function setCache($active){
        $name   = 'abchina:active:'.$active['actNo'];
        cache()->put($name, $active, 3600);
    }
}
