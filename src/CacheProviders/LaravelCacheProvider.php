<?php

namespace KDuma\emSzmalAPI\CacheProviders;

use Cache;
use Carbon\Carbon;

class LaravelCacheProvider implements CacheProviderInterface
{
    public function __construct(
        public readonly ?int $remember_for = 60
    ) { }

    public function cache(string $key, callable $callable): mixed
    {
        if ($this->remember_for) {
            return Cache::remember($key, Carbon::now()->addMinutes($this->remember_for), $callable);
        } else {
            return Cache::rememberForever($key, $callable);
        }
    }
}
