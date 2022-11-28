<?php

namespace KDuma\emSzmalAPI\CacheProviders;

class ArrayCacheProvider implements CacheProviderInterface
{
    protected array $cache = [];
    
    
    public function cache(string $key, callable $callable): mixed
    {
        if (!$this->has($key)) {
            $this->set($key, $callable());
        }

        return $this->get($key);
    }
    
    
    public function get(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }
    
    public function set(string $key, mixed $value): void
    {
        $this->cache[$key] = $value;
    }
    
    public function has(string $key): bool
    {
        return isset($this->cache[$key]);
    }
    
    
    public function all(): array
    {
        return $this->cache;
    }
    
    public function __debugInfo(): array
    {
        return $this->all();
    }
}