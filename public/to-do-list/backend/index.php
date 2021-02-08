<?php

define('BASE_PATH', realpath(__DIR__ . '/../../..'));

require __DIR__ . '/../../../vendor/autoload.php';

$app = new \Todo\Application($_SERVER);

echo $app->handle($_REQUEST);
