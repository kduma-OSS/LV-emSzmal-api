<?php

declare(strict_types=1);

namespace KDuma\emSzmalAPI\Laravel;

use Exception;
use KDuma\emSzmalAPI\emSzmalAPI;
use KDuma\emSzmalAPI\DTO\BankCredentials;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use KDuma\emSzmalAPI\CacheProviders\LaravelCacheProvider;
use KDuma\emSzmalAPI\CacheProviders\CacheProviderInterface;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    
    public function boot(): void
    {
        $this->handleConfigs();
    }

    public function register(): void
    {
        $this->app->singleton(CacheProviderInterface::class, function (Application $app): CacheProviderInterface {
            return $app->make(LaravelCacheProvider::class, [
                'remember_for' => config('emszmalapi.cache.remember_for'),
            ]);
        });

        $this->app->singleton(emSzmalAPI::class, function (Application $app): emSzmalAPI {
            $apiId = config('emszmalapi.license.api_id');
            $apiKey = config('emszmalapi.license.api_key');

            if (empty($apiId) || ! is_string($apiId)) {
                throw new \RuntimeException('emSzmal API: emszmalapi.license.api_id is not configured.');
            }

            if (empty($apiKey) || ! is_string($apiKey)) {
                throw new \RuntimeException('emSzmal API: emszmalapi.license.api_key is not configured.');
            }

            $api = new emSzmalAPI(
                api_id: $apiId,
                api_key: $apiKey,
                timeout: (int) config('emszmalapi.timeout', 120),
                cache_provider: $app->make(CacheProviderInterface::class),
            );

            $api->setDefaultBankCredentialsResolver(function ($identifier = 'default') {
                if (! config('emszmalapi.bank_credentials.'.$identifier)) {
                    throw new Exception('There is no credentials with id '.$identifier.'!');
                }

                return new BankCredentials(
                    provider: (int) config('emszmalapi.bank_credentials.'.$identifier.'.provider', 0),
                    login: (string) config('emszmalapi.bank_credentials.'.$identifier.'.login', ''),
                    password: (string) config('emszmalapi.bank_credentials.'.$identifier.'.password', ''),
                    user_context: (string) config('emszmalapi.bank_credentials.'.$identifier.'.user_context', ''),
                    token_value: (string) config('emszmalapi.bank_credentials.'.$identifier.'.token_value', '')
                );
            });

            return $api;
        });
    }
    
    public function provides(): array
    {
        return [CacheProviderInterface::class, emSzmalAPI::class];
    }

    private function handleConfigs(): void
    {
        $configPath = __DIR__.'/../../config/emszmalapi.php';

        $this->publishes([$configPath => config_path('emszmalapi.php')]);

        $this->mergeConfigFrom($configPath, 'emszmalapi');
    }
}
