<?php

namespace Tests\Features;

use Tests\TestCase;

class SortTodoTest extends TestCase
{
    public function testSortTodo()
    {
        $this->request('post', '/', ['title' => 'Foo']);
        $this->request('post', '/', ['title' => 'Foo 2']);

        $this->assertDatabaseHas('todos', ['id' => 1, 'title' => 'Foo', 'sort' => 1]);
        $this->assertDatabaseHas('todos', ['id' => 2, 'title' => 'Foo 2', 'sort' => 2]);

        $this->request('put', '/', ['sort' => [2, 1]]);

        $this->assertDatabaseHas('todos', ['id' => 1, 'title' => 'Foo', 'sort' => 2]);
        $this->assertDatabaseHas('todos', ['id' => 2, 'title' => 'Foo 2', 'sort' => 1]);
    }
}
