<?php namespace Analogue\MongoDB\Driver;

use Analogue\ORM\Drivers\DBAdapter;
use Jenssegers\Mongodb\Connection;
use Jenssegers\Mongodb\Query\Builder as QueryBuilder;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;

/**
 * MongoDB Driver for Analogue ORM. If multiple DB connections are 
 * involved, we'll treat each underlyin driver as a separate instance.
 */
class MongoDBAdapter implements DBAdapter {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Return a new Query instance for this driver
     * 
     * @return QueryAdapter 
     */
    public function getQuery()
    {
        $connection = $this->connection;

        return new MongoQueryBuilder($connection, $connection->getPostProcessor());
    }

    /**
     * Get the date format supported by the current connection
     * 
     * @return string
     */
    public function getDateFormat() : string
    {
        return "";
    }

    /**
     * Start a DB transaction on driver that supports it.
     * @return void
     */
    public function beginTransaction()
    {
        //$this->connection->beginTransaction();
    }

    /**
     * Commit a DB transaction on driver that supports it.
     * @return void
     */
    public function commit()
    {
        //$this->connection->commit();
    }

    /**
     * Rollback a DB transaction
     * @return void
     */
    public function rollback()
    {
        //$this->connection->rollback();
    }

    public function fromDatabase(array $results) : array
    {
        return $this->prepareValuesForHydration($results);
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

        return $this->timeStampToDateTime($value);
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
