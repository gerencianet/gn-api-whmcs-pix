<?php

use WHMCS\Database\Capsule;

/**
 * Validate table existance
 *
 * @param string $tableName
 * 
 * @return boolean
 */
function hasTable($tableName)
{
    try {
        $exists = Capsule::schema()->hasTable($tableName);

        return $exists;

    } catch (\Exception $e) {
        showException('DataBase Exception', array($e->getMessage()));
    }
}

/**
 * Create a new table on the schema
 *
 * @param string   $tableName
 * @param \Closure $callback
 */
function createTable($tableName, Closure $callback)
{
    try {
        Capsule::schema()->create($tableName, $callback);

    } catch (\Exception $e) {
        showException('DataBase Exception', array($e->getMessage()));
    }
}

if (function_exists('select')) {
   /** Function already loaded, skipping */
} else {
    /**
 * Set the columns to be selected
 *
 * @param string  $tableName
 * @param array   $conditions
 * @param array   $fields
 * @param boolean $getFirst
 * 
 * @return array
 */
function select($tableName, $conditions, $fields, $getFirst = true)
{
    try {
        $gerencianetData = Capsule::table($tableName);

        foreach ($conditions as $key => $value) {
            $gerencianetData = $gerencianetData->where($key, $value);
        }

        if ($getFirst) {
            $data = $gerencianetData->select($fields)->first();
        }
        else {
            $data = $gerencianetData->select($fields)->get();
        }

        return json_decode(json_encode($data), true);

    } catch (\Exception $e) {
        showException('DataBase Exception', array($e->getMessage()));
    }
}

}

/**
 * Execute a query for a single record by ID
 * 
 * @param string  $tableName
 * @param  string $idName 
 * @param  int    $idValue
 * @param  array  $columns
 * 
 * @return array
 */
function find($tableName, $idName, $idValue, $columns = ['*'])
{
    try {
        $data = Capsule::table($tableName)->where($idName, $idValue)->first($columns);

        return json_decode(json_encode($data), true);

    } catch (\Exception $e) {
        showException('DataBase Exception', array($e->getMessage()));
    }
}

/**
 * Get a single column's value from the first result of a query
 * 
 * @param string $tableName
 * @param array  $conditions
 * @param string $column
 * 
 * @return mixed
 */
function getValue($tableName, $conditions, $column)
{
    try {
        $gerencianetData = Capsule::table($tableName);

        foreach ($conditions as $key => $value) {
            $gerencianetData = $gerencianetData->where($key, $value);
        }

        $data = $gerencianetData->value($column);

        return $data;

    } catch (\Exception $e) {
        showException('DataBase Exception', array($e->getMessage()));
    }
}

if (function_exists('insert')) {
   /** Function already loaded, skipping */
} else {
    /**
     * Insert a new record into the database
     *
     * @param string $tableName
     * @param array  $dataToInsert
     */
    function insert($tableName, $dataToInsert)
    {
        try {
            Capsule::table($tableName)->insert($dataToInsert);

        } catch (\Exception $e) {
            showException('DataBase Exception', array($e->getMessage()));
        }
    }
}

if (function_exists('update')) {
   /** Function already loaded, skipping */
} else {
    /**
     * Update a record in the database
     *
     * @param string $tableName
     * @param array  $conditions
     * @param array  $dataToUpdate
     */
    function update($tableName, $conditions, $dataToUpdate)
    {
        try {
            $gerencianetData = Capsule::table($tableName);

            foreach ($conditions as $key => $value) {
                $gerencianetData = $gerencianetData->where($key, $value);
            }

            $gerencianetData->update($dataToUpdate);

        } catch (\Exception $e) {
            showException('DataBase Exception', array($e->getMessage()));
        } 
    }
}
if (function_exists('delete')) {
   /** Function already loaded, skipping */
} else {

    /**
     * Delete a record from the database
     *
     * @param string $tableName
     * @param array  $conditions
     */
    function delete($tableName, $conditions)
    {
        try {
            $gerencianetData = Capsule::table($tableName);

            foreach ($conditions as $key => $value) {
                $gerencianetData = $gerencianetData->where($key, $value);
            }

            $gerencianetData->delete();

        } catch (\Exception $e) {
            showException('DataBase Exception', array($e->getMessage()));
        } 
    }
}
