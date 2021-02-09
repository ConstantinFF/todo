<?php

namespace Tests\Features;

use Tests\TestCase;

class UpdateTodoTest extends TestCase
{
    public function testUpdateCompletedTodo()
    {
        $todo = [
            'id' => '1',
            'title' => 'Foo',
            'is_completed' => true,
            'sort' => '1',
        ];

        $this->request('post', '/', ['title' => 'Foo']);

        $this->assertEqualsCanonicalizing($todo, $this->request('put', '/1', ['is_completed' => true]));

        $this->assertDatabaseHas('todos', ['title' => 'Foo', 'is_completed' => 1]);
    }
}
