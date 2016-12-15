<?php namespace Analogue\MongoDB\Driver;

use Analogue\ORM\Drivers\DBAdapter;
use Jenssegers\Mongodb\Connection;
use Jenssegers\Mongodb\Query\Builder as QueryBuilder;

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
    public function getDateFormat()
    {
        //return $this->connection->getQueryGrammar()->getDateFormat();
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
}
