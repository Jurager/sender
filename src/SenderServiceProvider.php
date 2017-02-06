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
        $this->publishConfig();
        $this->publishLanguages();
        $this->mergeConfig();
        $this->mergeLanguages();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->registerSender();
        
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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sender');
    }

    /**
     * Publishes sender configs.
     *
     * @return void
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('sender.php'),
        ]);
    }

    /**
     * Merges user's and sender's languages assets.
     *
     * @return array
     */
    public function mergeLanguages()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'sender');
    }
    
    /**
     * Publishes sender configs.
     *
     * @return void
     */
    private function publishLanguages()
    {
        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/sender'),
        ]);
    }
}