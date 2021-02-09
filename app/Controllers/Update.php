<?php

namespace Todo\Controllers;

use Todo\Models\Todo;
use Todo\Support\Response;

class Update
{
    /**
     * Update Todo completed status.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke($request)
    {
        $attributes = (array) filter_var_array($request, [
            'is_completed' => FILTER_VALIDATE_BOOLEAN,
        ]);

        $todo = Todo::find($request['Router']['id']);

        $todo->is_completed = $attributes['is_completed'];
        $todo->save();

        return new Response($todo);
    }
}
