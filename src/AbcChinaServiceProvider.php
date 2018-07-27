<?php

namespace OkamiChen\TmsAbchina;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use OkamiChen\TmsAbchina\Event\ActiveDetail;
use OkamiChen\TmsAbchina\Event\ActiveFind;
use OkamiChen\TmsAbchina\Listener\ActiveFindListener;
use OkamiChen\TmsAbchina\Listener\ActiveDetailListener;

class AbcChinaServiceProvider extends ServiceProvider
{
    
    /**
     * @var array
     */
    protected $commands = [
        __NAMESPACE__.'\Console\Command\MonitorCommand',
        __NAMESPACE__.'\Console\Command\ExchangeCommand',
    ];
    
    protected $listens  = [
        
        ActiveDetail::class=> [
            ActiveDetailListener::class,
        ],
        ActiveFind::class=> [
            ActiveFindListener::class,
        ]
    ];


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoute();
        $this->regiterEvent();
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
    
    
    protected function regiterEvent(){
        if(!count($this->listens)){
            return false;
        }
        foreach ($this->listens as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
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
