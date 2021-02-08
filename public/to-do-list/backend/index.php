<?php

define('BASE_PATH', realpath(__DIR__ . '/../../..'));

require __DIR__ . '/../../../vendor/autoload.php';

$app = new \Todo\Application();

echo $app->handle($_SERVER, $_REQUEST);
