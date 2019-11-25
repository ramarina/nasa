<?php

namespace Nasa\Api;

use DI\Container;

/**
 * Class NasaWrapper
 * @package Nasa\Api
 */
class NasaWrapper
{
    /**
     * @var Container
     */
    private $container;

    /**
     * NasaWrapper constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $params
     * @param $date
     * @return array
     */
    public function getPhotos($params, $date)
    {
        //TODO get from App
        $config = require_once(__DIR__ . '/../config.php');

//        https://api.nasa.gov/mars-photos/api/v1/rovers/curiosity/photos?earth_date=2016-4-2&camera=NAVCAM&api_key=DEMO_KEY

        $url = $config['nasa_api']['api_url'] . "rovers/" . $params['rover'] . "/photos";

        //TODO get better solution for mappings
        $parameters = [];
        $parameters['earth_date'] = $date;
        $parameters['camera'] = $params['camera'];
        $parameters['api_key'] = $config['nasa_api']['api_key'];

        $result = json_decode($this->callCurl($url, $parameters));
        $return = [];

        if (!isset($result->photos)) {
            return [];
        }
        $sliced_photos = array_slice($result->photos, 0, $params['limit']);
        foreach ($sliced_photos as $member) {
            $return[] = $member->img_src;
        }
        return $return;

    }

    //TODO move this to separate class

    /**
     * @param $url
     * @param $parameters
     * @return bool|string
     */
    public function callCurl($url, $parameters)
    {
        try {
            if ($parameters) {
                $url = sprintf("%s?%s", $url, http_build_query($parameters));
            }
            $cURL = curl_init();

            curl_setopt($cURL, CURLOPT_URL, $url);
            curl_setopt($cURL, CURLOPT_HTTPGET, true);

            curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($cURL);

            if (curl_errno($cURL)) {
                print curl_error($cURL);
            }

            return $result;

        } catch (\Exception $e) {
            //TODO add Monolog and write $e->getMessage() to log
            echo 'Error with Nasa Api occurred';
        }

    }
}