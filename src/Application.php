<?php

namespace Nasa;

use DI\Container;

/**
 * Class Application
 * @package Nasa
 */
class Application
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var array
     */
    private $config;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->container = new Container();
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function run()
    {
        if (PHP_SAPI === 'cli') {
            $this->runCLI();
        } else {
            $this->runHTTP();
        }
    }

    public function runCLI()
    {
        echo "CLI started";
        try {
            //TODO call default route, write route parser and command handler for CLI
            $class = $this->container->get('Nasa\Controllers\Photos');
            $result = $class->showPhotos();
            var_dump($result);
        } catch (\Exception $e) {
            //TODO add Monolog and write $e->getMessage() to log
            echo 'Error with DI';
        }
        echo "CLI ended";
    }


    public function runHTTP()
    {
        $this->registerErrorHandler();

        //Boot Router
        $routes = require __DIR__ . '/routes.php';
        $response = $this->bootRouter($routes);
        var_dump($response);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function registerErrorHandler()
    {
        //TODO: move env to config
        $environment = 'development';

        $whoops = $this->container->get('\Whoops\Run');
        if ($environment !== 'production') {
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        } else {
            $whoops->pushHandler(function ($e) {
                echo 'Todo: Friendly error page and send an email to the developer';
            });
        }
        $whoops->register();
    }

    /**
     * @param $routes
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Http\MissingRequestMetaVariableException
     */
    public function bootRouter($routes)
    {
        $router = $this->container->get('Nasa\Router');
        return $router->boot($this->container, $routes);
    }
}