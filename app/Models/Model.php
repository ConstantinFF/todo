<?php

namespace Todo\Models;

use PDO;
use Todo\Support\Query;
use Todo\Support\Database;

class Model
{
    /**
     * List all database records.
     *
     * @param array $options
     * @return array
     */
    public static function all($options = [])
    {
        return Database::getConnection()
            ->query(sprintf('SELECT * FROM %s %s', static::$table, Query::orderQuery($options['orderBy'] ?? null)))
            ->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Create database record.
     *
     * @param array $attributes
     * @return self
     */
    public static function create($attributes = [])
    {
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            Query::insertFieldsQuery($attributes),
            Query::insertFieldsQuery($attributes, true)
        );

        Database::getConnection()
            ->prepare(Query::insertNewRecord(static::$table, $attributes))
            ->execute($attributes);

        return static::find(Database::getConnection()->lastInsertId());
    }

    /**
     * Find record by id
     *
     * @param int $id
     * @return self
     */
    public static function find(int $id)
    {
        $db = Database::getConnection()->prepare(Query::findById(static::$table));
        $db->execute([$id]);
        $db->setFetchMode(PDO::FETCH_CLASS, static::class);

        return $db->fetch(PDO::FETCH_CLASS);
    }

    /**
     * Find first record
     *
     * @param array $options
     * @return self
     */
    public static function first($options = [])
    {
        $db = Database::getConnection()->prepare(sprintf('SELECT * FROM %s %s', static::$table, Query::orderQuery($options['orderBy'] ?? null)));
        $db->execute();
        $db->setFetchMode(PDO::FETCH_CLASS, static::class);

        return $db->fetch(PDO::FETCH_CLASS);
    }

    public function save()
    {
        $data = (array) $this;

        return Database::getConnection()
            ->prepare(sprintf(
                'UPDATE %s SET %s WHERE id=:id',
                static::$table,
                self::updateFieldsQuery($data)
            ))
            ->execute($data);
    }

    public function __serialize(): array
    {
        return $this;
    }
}
