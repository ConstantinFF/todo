<?php

return [
    '/' => [
        'get' => \Todo\Controllers\Index::class,
        'post' => \Todo\Controllers\Create::class,
    ],
    '/(?P<id>\d+)' => [
        'put' => \Todo\Controllers\Update::class,
        'delete' => \Todo\Controllers\Delete::class,
    ],
];
