<?php

namespace KDuma\emSzmalAPI\CacheProviders;

class NoCacheProvider implements CacheProviderInterface
{
    public function cache(string $key, callable $callable): mixed
    {
        return $callable();
    }
}