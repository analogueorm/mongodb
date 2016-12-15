<?php namespace Analogue\MongoDB\Driver;

use Jenssegers\Mongodb\Query\Builder;
use MongoDB\BSON\ObjectID;

class MongoQueryBuilder extends Builder {

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array   $values
     * @param  string  $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        $value = parent::insertGetId($values, $sequence);
        
        // Convert ObjectID to string.
        if ($value instanceof ObjectID) {
            return (string) $value;
        }

        return $value;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return array|static[]|Collection
     */
    public function get($columns = [])
    {
        $results = parent::get($columns);

        return $results->map(function($item) {
            return array_map(function($attribute) {
                if ($attribute instanceof ObjectID) {
                    return (string) $attribute;
                }
            }, $item);
        });
    }
}
