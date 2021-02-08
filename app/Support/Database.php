<?php

namespace Todo\Support;

use PDO;

class Database
{
    private static $connection;
    private $pdo;

    public function __construct()
    {
        $hasMigrated = file_exists(ltrim(getenv('DATABASE'), 'sqlite:'));

        $this->pdo = new PDO(getenv('DATABASE'), '', '', [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        if (! $hasMigrated) {
            $this->migrate();
        }
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }

    public static function getConnection()
    {
        return self::$connection = self::$connection ?? new Database();
    }

    private function migrate()
    {
        $migration = file_get_contents(BASE_PATH . '/database/migration.sql');

        $this->pdo->exec($migration);
    }
}
