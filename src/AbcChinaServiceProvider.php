<?php

namespace OkamiChen\TmsAbchina;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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
        $this->registerRoute();
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
    
    protected function registerRoute(){
        $attributes = [
            'prefix'     => '/yh-web/',
            'namespace'  => __NAMESPACE__.'\Controller',
            'middleware' => 'web',
        ];
        Route::group($attributes, function (Router $router) {
            $router->any('/jedis/resetSessionExpire', 'AbchinaController@doSession');
        });
        
    }
}
