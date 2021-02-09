<?php

namespace Tests\Features;

use Tests\TestCase;

class IndexTodoTest extends TestCase
{
    public function testGetTodosList()
    {
        $todo = $this->request('post', '/', ['title' => 'Foo']);

        $this->assertEqualsCanonicalizing(['data' => [$todo]], $this->request('get', '/'));
    }
}
