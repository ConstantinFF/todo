<?php

namespace Todo\Controllers;

use Todo\Models\Todo;
use Todo\Support\Response;

class Delete
{
    /**
     * Delete Todo.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke($request)
    {
        $todo = Todo::find($request['Router']['id']);
        $todo->delete();

        return new Response(['success' => true]);
    }
}
