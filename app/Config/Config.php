<?php

namespace App\Config;

use App\Config\Loaders\Loader;

class Config
{
    /**
     * Main config
     *
     * @var array
     */
    protected $config = [
        'app' => [
            'name' => 'project-name'
        ],
        'db' => [
            'host' => 'localhost',
            'database' => 'autowiring',
            'username' => 'root',
            'password' => 'password'
        ]
    ];

    /**
     * Cache
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Get a configuration file
     *
     * @param  string $key
     * @param  null   $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->existsInCache($key)) {
            return $this->fromCache($key);
        }

        return $this->addToCache(
            $key, $this->extractFromConfig($key) ?? $default
        );
    }

    /**
     * Extract value from config
     *
     * @param  string $key
     * @return array|mixed|void
     */
    protected function extractFromConfig(string $key)
    {
        $filtered = $this->config;

        foreach (explode('.', $key) as $segment) {
            if ($this->exists($filtered, $segment)) {
                $filtered = $filtered[$segment];
                continue;
            }

            return;
        }

        return $filtered;
    }

    /**
     * Add to cache
     *
     * @param string $key
     * @param        $value
     * @return mixed
     */
    protected function addToCache(string $key, $value)
    {
        $this->cache[$key] = $value;

        return $value;
    }

    /**
     * If value exists in the cache
     *
     * @param  string $key
     * @return bool
     */
    protected function existsInCache(string $key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * Get from cache
     *
     * @param  string $key
     * @return mixed
     */
    protected function fromCache(string $key)
    {
        return $this->cache[$key];
    }

    /**
     * If the key exists in a config array
     *
     * @param  array $config
     * @param  string $key
     * @return bool
     */
    protected function exists(array $config, string $key)
    {
        return array_key_exists($key, $config);
    }
}
