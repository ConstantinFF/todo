<?php

namespace Todo\Controllers;

use Todo\Models\Todo;
use Todo\Support\Response;

class Create
{
    /**
     * Create Todo.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke($request)
    {
        $attributes = (array) filter_var_array($request, [
            'title' => FILTER_SANITIZE_ENCODED,
        ]);

        $this->validate($attributes);

        $lastTodo = Todo::first(['orderBy' => ['sort', 'DESC']]);
        $attributes['sort'] = $lastTodo ? $lastTodo->sort + 1 : 1;

        $todo = Todo::create($attributes);

        return new Response($todo);
    }

    private function validate($attributes)
    {
        if (empty($attributes['title'])) {
            throw new \Exception('Title is required', 1);
        }
    }
}
