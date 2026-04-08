<?php

declare(strict_types=1);

namespace KDuma\emSzmalAPI\CacheProviders;

class NoCacheProvider implements CacheProviderInterface
{
    public function cache(string $key, callable $callable): mixed
    {
        return $callable();
    }
}