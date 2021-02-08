<?php

namespace Todo\Controllers;

use Todo\Support\Response;

class Index
{
    public function __invoke()
    {
        return new Response([]);
    }
}
