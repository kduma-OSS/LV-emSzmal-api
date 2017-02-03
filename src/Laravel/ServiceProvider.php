<?php

namespace KDuma\emSzmalAPI\Laravel;

use Exception;
use KDuma\emSzmalAPI\emSzmalAPI;
use KDuma\emSzmalAPI\BankCredentials;
use Illuminate\Contracts\Foundation\Application;
use KDuma\emSzmalAPI\CacheProviders\LaravelCacheProvider;
use KDuma\emSzmalAPI\CacheProviders\CacheProviderInterface;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CacheProviderInterface::class, function (Application $app) {
            return new LaravelCacheProvider(config('emszmalapi.cache.remember_for'));
        });

        $this->app->singleton(emSzmalAPI::class, function (Application $app) {
            $api = new emSzmalAPI(config('emszmalapi.license.api_id'), config('emszmalapi.license.api_key'), config('emszmalapi.timeout', 120));
            $api->setCacheProvider($app->make(CacheProviderInterface::class));
            $api->setDefaultBankCredentialsResolver(function ($identifier = 'default') {
                if (! config('emszmalapi.bank_credentials.'.$identifier)) {
                    throw new Exception('There is no credentials with id '.$identifier.'!');
                }

                return new BankCredentials(
                    config('emszmalapi.bank_credentials.'.$identifier.'.provider'),
                    config('emszmalapi.bank_credentials.'.$identifier.'.login'),
                    config('emszmalapi.bank_credentials.'.$identifier.'.password'),
                    config('emszmalapi.bank_credentials.'.$identifier.'.user_context')
                );
            });

            return $api;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [CacheProviderInterface::class, emSzmalAPI::class];
    }

    private function handleConfigs()
    {
        $configPath = __DIR__.'/../../config/emszmalapi.php';

        $this->publishes([$configPath => config_path('emszmalapi.php')]);

        $this->mergeConfigFrom($configPath, 'emszmalapi');
    }
}
