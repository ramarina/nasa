<?php

namespace Nasa\Controllers;

use Nasa\Api\NasaWrapper;
use Nasa\Cache\MemCache;

/**
 * Class Photos
 * @package Nasa\Controllers
 */
class Photos
{

    const ROVER = 'rover';
    const CAMERA = 'camera';
    const DAY_RANGE = 'dayRange';
    const LIMIT = 'limit';
    const DEFAULT_LIMIT = 3;
    const DEFAULT_ROVER = 'curiosity';
    const DEFAULT_CAMERA = 'NAVCAM';
    const DEFAULT_DAY_RANGE = 2;
    /**
     * @var NasaWrapper
     */
    private $nasaApi;
    /**
     * @var MemCache
     */
    private $cache;

    /**
     * Photos constructor.
     * @param NasaWrapper $nasaApi
     * @param MemCache $cache
     */
    public function __construct(NasaWrapper $nasaApi, MemCache $cache)
    {
        $this->nasaApi = $nasaApi;
        $this->cache = $cache;
    }

    /**
     * @param array $params
     * @return array
     */
    public function showPhotos($params = [])
    {
        if (empty($params)) {
            $params = [
                self::ROVER => self::DEFAULT_ROVER,
                self::CAMERA => self::DEFAULT_CAMERA,
                self::DAY_RANGE => self::DEFAULT_DAY_RANGE,
                self::LIMIT => self::DEFAULT_LIMIT
            ];
        }

        return $this->getPhotos($params);
    }

    /**
     * @param $params
     * @return array
     */
    private function getPhotos($params)
    {
        try {
            //$today = date('Y-m-d');
            $today = '2016-4-2';
            $result = [];

            for ($i = $params[self::DAY_RANGE]; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days", strtotime($today)));

                //TODO move to getKey func
                $key = $params[self::ROVER] . $params[self::CAMERA] . $params[self::LIMIT] . $date;
                //TODO fix bug for appropriate nesting in result
                if ($this->cache->getCache($key)) {
                    $result[$date] = $this->cache->getCache($key);
                    continue;
                }

                $result[$date] = $this->nasaApi->getPhotos($params, $date);
                $this->cache->setCache($key, $result);
            }
            return $result;
        } catch (\Exception $e) {
            //TODO add Monolog and write $e->getMessage() to log
            echo 'Error with Memcached occurred';
        }

    }
}