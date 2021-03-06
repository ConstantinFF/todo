<?php

namespace Todo\Controllers;

use Todo\Models\Todo;
use Todo\Support\Response;

class Index
{
    /**
     * List all Todos.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke()
    {
        return new Response(['data' => Todo::all(['orderBy' => ['sort', 'ASC']])]);
    }
}
