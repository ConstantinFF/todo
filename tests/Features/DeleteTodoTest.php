<?php

namespace Tests\Features;

use Tests\TestCase;

class DeleteTodoTest extends TestCase
{
    public function testDeleteTodo()
    {
        $this->request('post', '/', ['title' => 'Foo']);
        $this->request('delete', '/1');

        $this->assertDatabaseMissing('todos', ['title' => 'Foo']);
    }
}
