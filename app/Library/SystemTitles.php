<?php

namespace App\Library;

use Illuminate\Support\Facades\Cache;

/**
 * Abstract class, for getting titles, across all systems 
 */
abstract class SystemTitles //interface would be better than abstract class, but I find abstract classes more convenient in use for me
{
    /**
     * Holds singleton to object
     */
    protected static $singleton;

    /**
     * Get object
     * @param mixed ...$args
     * @return static
     */
    
    /**
     * Cache time
     */
    const CACHE_TIME = 60 * 15;

    /**
     * How many repeat, if request fails
     */
    const REPEAT = 3;

    /**
     * Titles
     * @var array
     */
    protected $titles = [];

    /**
     * Get instance
     * @param mixed ...$args
     * @return static
     */
    public static function getInstance(...$args)
    {
        if (!static::$singleton) {
            static::$singleton = new static(...$args);
        }

        return static::$singleton;
    }

    /**
     * Get titles
     * @return array
     */
    public function get()
    {
        //if titles were acquired in request lifecycle
        if ($this->titles) {
            return $this->titles;
        }

        //repeat on failure
        for ($i = 0; $i < max(static::REPEAT, 1); $i++) {
            $titles = $this->getTitles();
            if ($titles) {
                break;
            }
        }

        //try to get from cache
        if (!$titles) {
            $titles = cache($this->getCacheKey());
        }

        //if succeed, cache results, and save to use in request lifecycle more than once
        if ($titles) {
            Cache::put($this->getCacheKey(), $titles, self::CACHE_TIME);
            $this->titles = $titles;
        }

        return $titles;
    }

    /**
     * Prepare key used in cache
     * @return string
     */
    protected function getCacheKey()
    {
        return 'system_titles|' . get_class($this);
    }

    /**
     * Get titles from system
     * @return array|boolean
     */
    protected function getTitles()
    {
    }
}
