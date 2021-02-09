<?php

namespace Todo\Support;

class Query
{
    /**
     * SQL query for inserting new record
     *
     * @param string $table
     * @param array $attributes
     * @return string
     */
    public static function insertNewRecord($table, $attributes): string
    {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            Query::insertFieldsQuery($attributes),
            Query::insertFieldsQuery($attributes, true)
        );
    }

    /**
     * SQL insert fields
     *
     * @param array $attributes
     * @param bool $isValue
     * @return string
     */
    public static function insertFieldsQuery($attributes, $isValue = false): string
    {
        $fields = array_keys($attributes);

        $valueColon = $isValue ? ':' : null;

        return $valueColon . implode(", {$valueColon}", $fields);
    }

    /**
     * SQL query for search record by id
     *
     * @param array $attributes
     * @param bool $isValue
     * @return string
     */
    public static function findById($table): string
    {
        return sprintf('SELECT * FROM %s WHERE id=?', $table);
    }

    /**
     * SQL query for search record by id
     *
     * @param array $attributes
     * @param bool $isValue
     * @return string
     */
    public static function findAll($table, $options): string
    {
        return sprintf('SELECT * FROM %s %s', $table, Query::orderQuery($options['orderBy'] ?? null));
    }

    public static function whereAnd($attributes): string
    {
        $fields = array_keys($attributes);

        $queryFields = array_map(fn ($field) => "{$field}=:{$field}", $fields);

        return implode(' AND ', $queryFields);
    }

    public static function deleteRecord($table): string
    {
        return sprintf(
            'DELETE FROM %s WHERE id=:id',
            $table
        );
    }

    public static function sortRecords($table, $sort): string
    {
        $i = 0;

        $sortQuery = array_reduce($sort, function ($carry, $id) use (&$i) {
            $i++;

            return $carry . " WHEN id = {$id} THEN {$i}";
        });

        return sprintf(
            'UPDATE %s SET sort = CASE %s END WHERE 1',
            $table,
            $sortQuery
        );
    }

    public static function updateRecord($table, $attributes): string
    {
        return sprintf(
            'UPDATE %s SET %s WHERE id=:id',
            $table,
            self::updateFieldsQuery($attributes)
        );
    }

    public static function updateFieldsQuery($attributes): string
    {
        unset($attributes['id']);

        $fields = array_keys($attributes);

        $queryFields = array_map(fn ($field) => "{$field}=:{$field}", $fields);

        return implode(', ', $queryFields);
    }

    public static function orderQuery($order): string
    {
        if (! $order) {
            null;
        }
        
        [$column, $direction] = (array) $order;

        $direction = $direction ?? 'ASC';

        return "ORDER BY {$column} {$direction}";
    }
}
