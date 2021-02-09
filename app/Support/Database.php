<?php

namespace Todo\Support;

use PDO;

class Database
{
    private static $connection;
    private $pdo;

    /**
     * Create database instance with PDO.
     * If database does not exists - run migrations.
     */
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

    /**
     * Handle calls to PDO.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }

    /**
     * Retrieve currently open database connection create new.
     *
     * @param string $name
     * @param array $arguments
     * @return self
     */
    public static function getConnection()
    {
        return self::$connection = self::$connection ?? new self();
    }

    /**
     * Close open database connection.
     */
    public static function closeConnection()
    {
        self::$connection = null;
    }

    /**
     * Execute database migrations.
     */
    private function migrate()
    {
        $migration = file_get_contents(BASE_PATH . '/database/migration.sql');

        $this->pdo->exec($migration);
    }
}
