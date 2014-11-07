<?php namespace mrkmg\NativeSass;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CompilerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register the package namespace
        $this->package('mrkmg/native-sass');

        // Read settings from config file
        $config = $this->app->config->get('native-sass::config', array());

        // Apply config settings
        $this->app['mrkmg.nativesass']->config($config);

        // Add 'NativeSass' facade alias
        AliasLoader::getInstance()->alias('NativeSass', 'mrkmg\NativeSass\Facades\NativeSass');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind 'mrkmg.nativesass' shared component to the IoC container
        $this->app->singleton('mrkmg.nativesass', function ($app) {
            return new Compiler();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
