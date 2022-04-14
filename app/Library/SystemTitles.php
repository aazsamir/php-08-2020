<?php

namespace App\Library;

use Illuminate\Support\Facades\Cache;

/**
 * Abstract class, for getting titles, across all systems 
 */
abstract class SystemTitles
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

    const CACHE_TIME = 60 * 15;

    const REPEAT = 3;

    /**
     * Titles
     * @var array
     */
    protected $titles = [];
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
        if ($this->titles) {
            return $this->titles;
        }

        for ($i = 0; $i < static::REPEAT; $i++) {
            $titles = $this->getTitles();
            if ($titles) {
                break;
            }
        }

        if (!$titles) {
            $titles = cache($this->getCacheKey());
        }

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
