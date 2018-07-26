<?php

namespace OkamiChen\TmsAbchina;

use Illuminate\Support\ServiceProvider;

class AbcChinaServiceProvider extends ServiceProvider
{
    
    /**
     * @var array
     */
    protected $commands = [
        __NAMESPACE__.'\Console\Command\MonitorCommand',
        __NAMESPACE__.'\Console\Command\ExchangeCommand',
    ];
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
