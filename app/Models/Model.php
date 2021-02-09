<?php

namespace Todo\Models;

use PDO;
use Todo\Support\Query;
use Todo\Support\Database;

class Model
{
    /**
     * Return list of all database records.
     *
     * @param array $options
     * @return array
     */
    public static function all($options = [])
    {
        return Database::getConnection()
            ->query(Query::findAll(static::$table, $options))
            ->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Create database record. And return it from DB.
     *
     * @param array $attributes
     * @return self
     */
    public static function create($attributes = [])
    {
        Database::getConnection()
            ->prepare(Query::insertNewRecord(static::$table, $attributes))
            ->execute($attributes);

        return static::find(Database::getConnection()->lastInsertId());
    }

    /**
     * Find record by id.
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
     * Find first record.
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

    /**
     * Save current record.
     *
     * @return boolean
     */
    public function save()
    {
        $data = (array) $this;

        return Database::getConnection()
            ->prepare(Query::updateRecord(static::$table, $data))
            ->execute($data);
    }

    /**
     * Delete current record.
     *
     * @return boolean
     */
    public function delete()
    {
        return Database::getConnection()
            ->prepare(Query::deleteRecord(static::$table))
            ->execute(['id' => $this->id]);
    }

    /**
     * Update sort fields.
     *
     * @param array $sort
     * @return boolean
     */
    public function updateSort($sort)
    {
        return Database::getConnection()
            ->prepare(Query::sortRecords(static::$table, $sort))
            ->execute();
    }

    /**
     * Serialize model into array of current object.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return $this;
    }
}
