<?php

namespace Tests;

use Todo\Support\Query;
use Todo\Support\Database;
use Mockery\Adapter\Phpunit\MockeryTestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $app;

    protected function setUp(): void
    {
        define('BASE_PATH', realpath(__DIR__ . '/..'));

        $this->app = new \Todo\Application();
        
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Database::closeConnection();

        parent::tearDown();
    }

    protected function request($method, $path, $data = [])
    {
        $server = [
            'REQUEST_METHOD' => $method,
            'PATH_INFO' => $path,
        ];

        return json_decode(json_encode(
            $this->app->handle($server, $data)->body()
        ), true);
    }

    protected function assertDatabaseHas($table, array $data, $count = 1)
    {
        $db = Database::getConnection()
            ->prepare(sprintf('SELECT COUNT(*) FROM %s WHERE %s', $table, Query::whereAnd($data)));
        $db->execute($data);

        return $this->assertEquals($count, $db->fetchColumn());
    }

    protected function assertDatabaseMissing($table, array $data)
    {
        return $this->assertDatabaseHas($table, $data, 0);
    }
}
