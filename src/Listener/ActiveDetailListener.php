<?php

namespace OkamiChen\TmsAbchina\Listener;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OkamiChen\TmsAbchina\Event\ActiveDetail;

class ActiveDetailListener implements ShouldQueue
{
    use InteractsWithQueue;
    
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
    public function handle(ActiveDetail $event)
    {
        $this->setCache($event->getRaw());
    }
    
    protected function setCache($active){
        $name   = 'abchina:detail:'.$active['actNo'];
        cache()->put($name, $active, 3600);
    }
}
