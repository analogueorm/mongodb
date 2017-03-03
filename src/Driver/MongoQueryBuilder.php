<?php namespace Analogue\MongoDB\Driver;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
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

        if (! $value instanceof DateTime) {
            $value = $this->timeStampToDateTime($value);
        }

        return new UTCDateTime($value->getTimestamp() * 1000);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function timeStampToDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Carbon) {
            return $value;
        }

         // If the value is already a DateTime instance, we will just skip the rest of
         // these checks since they will be a waste of time, and hinder performance
         // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return new Carbon(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if ($this->isStandardDateFormat($value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        return Carbon::createFromFormat(
            $this->getDateFormat(), $value
        );
    }
}
