<?php

namespace Nasa\Cache;

/**
 * Class MemCache
 * @package Nasa\Cache
 */
class MemCache
{
    /**
     * @var \Memcached
     */
    private $memcache;

    /**
     * MemCache constructor.
     */
    public function __construct()
    {
        $this->memcache = new \Memcached(); //TODO move on app start, instantiate with Container
        $this->memcache->addServer('localhost', 11211); //TODO get params from config
    }

    /**
     * @param $key
     * @param $var
     * @param int $expire
     */
    public function setCache($key, $var, $expire = 0)
    {
        $this->memcache->set($key, $var, $expire);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getCache($key)
    {
        return $this->memcache->get($key);
    }

}