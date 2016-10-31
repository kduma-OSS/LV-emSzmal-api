<?php

namespace KDuma\emSzmalAPI\CacheProviders;

use Cache;

class LaravelCacheProvider implements CacheProviderInterface
{
    /**
     * @var int|null
     */
    private $remember_for;

    /**
     * LaravelCacheProvider constructor.
     *
     * @param int|null $remember_for
     */
    public function __construct($remember_for = 60)
    {
        $this->remember_for = $remember_for;
    }

    /**
     * @param string   $key
     * @param callable $callable
     *
     * @return mixed
     */
    public function cache($key, callable $callable)
    {
        if ($this->remember_for) {
            return Cache::remember($key, $this->remember_for, $callable);
        } else {
            return Cache::rememberForever($key, $callable);
        }
    }
}
