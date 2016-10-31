<?php


    namespace KDuma\emSzmalAPI\CacheProviders;


    /**
     * Interface CacheProviderInterface
     *
     * @package KDuma\emSzmalAPI\CacheProviders
     */
    interface CacheProviderInterface
    {
        /**
         * @param string   $key
         * @param callable $callable
         *
         * @return mixed
         */
        public function cache($key, callable $callable);
    }