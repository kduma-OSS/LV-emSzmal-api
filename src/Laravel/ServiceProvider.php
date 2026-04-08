<?php

declare(strict_types=1);

namespace KDuma\emSzmalAPI\Laravel;

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

            if (! is_string($apiId) || trim($apiId) === '') {
                throw new \RuntimeException('emSzmal API: emszmalapi.license.api_id must be a non-empty string.');
            }

            if (! is_string($apiKey) || trim($apiKey) === '') {
                throw new \RuntimeException('emSzmal API: emszmalapi.license.api_key must be a non-empty string.');
            }

            $api = new emSzmalAPI(
                api_id: $apiId,
                api_key: $apiKey,
                timeout: (int) config('emszmalapi.timeout', 120),
                cache_provider: $app->make(CacheProviderInterface::class),
            );

            $api->setDefaultBankCredentialsResolver(function ($identifier = 'default') {
                $prefix = 'emszmalapi.bank_credentials.'.$identifier;

                if (! config($prefix)) {
                    throw new \RuntimeException(
                        'emSzmal API: '.$prefix.' is not configured.'
                    );
                }

                $provider = config($prefix.'.provider');
                if (! is_numeric($provider) || (int) $provider <= 0) {
                    throw new \RuntimeException(
                        'emSzmal API: '.$prefix.'.provider must be a positive integer.'
                    );
                }

                $login = config($prefix.'.login');
                if (! is_string($login) || trim($login) === '') {
                    throw new \RuntimeException(
                        'emSzmal API: '.$prefix.'.login must be a non-empty string.'
                    );
                }

                $password = config($prefix.'.password');
                if (! is_string($password) || trim($password) === '') {
                    throw new \RuntimeException(
                        'emSzmal API: '.$prefix.'.password must be a non-empty string.'
                    );
                }

                return new BankCredentials(
                    provider: (int) $provider,
                    login: $login,
                    password: $password,
                    user_context: (string) config($prefix.'.user_context', ''),
                    token_value: (string) config($prefix.'.token_value', '')
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
