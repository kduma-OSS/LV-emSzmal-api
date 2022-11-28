<?php

namespace KDuma\emSzmalAPI\CacheProviders;

interface CacheProviderInterface
{
    public function cache(string $key, callable $callable): mixed;
}
