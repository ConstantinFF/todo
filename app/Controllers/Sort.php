<?php

namespace Todo\Controllers;

use Todo\Models\Todo;
use Todo\Support\Response;

class Sort
{
    /**
     * Sort all Todos.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke($request)
    {
        $attributes = (array) filter_var_array($request['sort'], FILTER_VALIDATE_INT);

        $todos = Todo::updateSort($attributes);

        return new Response(['data' => Todo::all(['orderBy' => ['sort', 'ASC']])]);
    }
}
