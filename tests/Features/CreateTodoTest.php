<?php

namespace Tests\Features;

use Tests\TestCase;

class CreateTodoTest extends TestCase
{
    public function testInsertTodo()
    {
        $todo = [
            'id' => '1',
            'is_completed' => '0',
            'sort' => '1',
            'title' => 'Foo',
        ];

        $this->assertEqualsCanonicalizing($todo, $this->request('post', '/', ['title' => 'Foo']));

        $this->assertDatabaseHas('todos', $todo);
    }
}
