<?php

declare(strict_types=1);

namespace KDuma\emSzmalAPI\CacheProviders;

interface CacheProviderInterface
{
    public function cache(string $key, callable $callable): mixed;
}
