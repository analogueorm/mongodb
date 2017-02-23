<?php namespace Analogue\MongoDB\Driver;

use Carbon\Carbon;
use DateTime;
use Jenssegers\Mongodb\Query\Builder;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

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
        $this->prepareValuesForSave($values);

        $value = parent::insertGetId($values, $sequence);

        // Convert ObjectID to string.
        if ($value instanceof ObjectID) {
            return (string) $value;
        }

        return $value;
    }

    /**
     * Perform an insert operation
     * 
     * @param  array  $values
     * @return int
     */
    public function insert(array $values)
    {
        $this->prepareValuesForSave($values);
        return parent::insert($values);
    }

    /**
     * Perform an Update operation
     * @param  array  $values  
     * @param  array  $options
     * @return int
     */
    public function update(array $values, array $options = [])
    {
        $this->prepareValuesForSave($values);
        return parent::update($values, $options);
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
        
        return $results->map([$this, 'prepareValuesForHydration']);
    }

    /**
     * Transform values in mongoDB friendly values
     * 
     * @param  array  $values
     * @return array
     */
    public function prepareValuesForSave(array &$values)
    {
        $host = $this;

        array_walk_recursive($values, function(&$item) use ($host) {
            if($item instanceof DateTime) {
                $item = $host->fromDateTime($item);
            }
        });

        return $values;
    }

    /**
     * Convert values to Analogue friendly values
     * 
     * @param  array  $values
     * @return array $values
     */
    public function prepareValuesForHydration(array $values)
    {
        $host = $this;

        array_walk_recursive($values, function(&$item) use ($host) {
            if($item instanceof UTCDateTime) {
                $item = $host->asDateTime($item);
            }
            if ($item instanceof ObjectID) {
                $item = (string) $item;
            }
        });

        return $values;
    }

    /**
     * Convert a DateTime to a storable UTCDateTime object.
     *
     * @param  DateTime|int  $value
     * @return UTCDateTime
     */
    protected function fromDateTime($value)
    {
        // If the value is already a UTCDateTime instance, we don't need to parse it.
        if ($value instanceof UTCDateTime) {
            return $value;
        }

        // Let Eloquent convert the value to a DateTime instance.
        if (! $value instanceof DateTime) {
            $value = parent::asDateTime($value);
        }

        return new UTCDateTime($value->getTimestamp() * 1000);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return DateTime
     */
    protected function asDateTime($value)
    {
        // Convert UTCDateTime instances.
        if ($value instanceof UTCDateTime) {
            return Carbon::createFromTimestamp($value->toDateTime()->getTimestamp());
        }

        return parent::asDateTime($value);
    }
}
