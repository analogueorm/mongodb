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

}
