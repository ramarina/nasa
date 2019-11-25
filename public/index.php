<?php

require __DIR__ . '/../vendor/autoload.php';

//Instantiate application with configuration
$config = require __DIR__ . '/../src/config.php';

$app = new Nasa\Application($config);

$app->run();