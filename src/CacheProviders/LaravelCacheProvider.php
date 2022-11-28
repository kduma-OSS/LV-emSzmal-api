<?php

namespace KDuma\emSzmalAPI\CacheProviders;

use Illuminate\Cache\CacheManager;
use Carbon\Carbon;

class LaravelCacheProvider implements CacheProviderInterface
{
    public function __construct(
        public readonly CacheManager $cache_manager,
        public readonly ?int $remember_for = 60,
    ) { }

    public function cache(string $key, callable $callable): mixed
    {
        if ($this->remember_for) {
            return $this->cache_manager->remember($key, Carbon::now()->addMinutes($this->remember_for), $callable);
        } else {
            return $this->cache_manager->rememberForever($key, $callable);
        }
    }
}
