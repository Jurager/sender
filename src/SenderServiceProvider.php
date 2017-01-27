<?php 

namespace Jurager\Sender;

use Illuminate\Support\ServiceProvider;


class SenderServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('sender.php'),
        ]);
       
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->registerSender();
        $this->mergeConfig();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerSender()
    {
        $this->app->bind('sender', function ($app) {
            return new Sender($app);
        });

        $this->app->alias('sender', 'Jurager\Sender');
    }



    /**
     * Merges user's and sender's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'sender'
        );
    }
    
}