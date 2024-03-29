<?php

namespace KDuma\emSzmalAPI\Laravel;

use Exception;
use KDuma\emSzmalAPI\emSzmalAPI;
use KDuma\emSzmalAPI\DTO\BankCredentials;
use Illuminate\Contracts\Foundation\Application;
use KDuma\emSzmalAPI\CacheProviders\LaravelCacheProvider;
use KDuma\emSzmalAPI\CacheProviders\CacheProviderInterface;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


class ServiceProvider extends LaravelServiceProvider
{
    protected $defer = true;
    
    public function boot()
    {
        $this->handleConfigs();
    }
    
    public function register()
    {
        $this->app->singleton(CacheProviderInterface::class, function (Application $app) {
            return $app->make(LaravelCacheProvider::class, [
                'remember_for' => config('emszmalapi.cache.remember_for'),
            ]);
        });

        $this->app->singleton(emSzmalAPI::class, function (Application $app) {
            $api = new emSzmalAPI(
                api_id: config('emszmalapi.license.api_id'),
                api_key: config('emszmalapi.license.api_key'),
                timeout: config('emszmalapi.timeout', 120),
                cache_provider: $app->make(CacheProviderInterface::class),
            );
            
            $api->setDefaultBankCredentialsResolver(function ($identifier = 'default') {
                if (! config('emszmalapi.bank_credentials.'.$identifier)) {
                    throw new Exception('There is no credentials with id '.$identifier.'!');
                }

                return new BankCredentials(
                    provider: config('emszmalapi.bank_credentials.'.$identifier.'.provider') ?? '',
                    login: config('emszmalapi.bank_credentials.'.$identifier.'.login') ?? '',
                    password: config('emszmalapi.bank_credentials.'.$identifier.'.password') ?? '',
                    user_context: config('emszmalapi.bank_credentials.'.$identifier.'.user_context') ?? '',
                    token_value: config('emszmalapi.bank_credentials.'.$identifier.'.token_value') ?? ''
                );
            });

            return $api;
        });
    }
    
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
