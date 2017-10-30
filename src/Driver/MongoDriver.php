<?php namespace Analogue\MongoDB\Driver;

use Analogue\ORM\Drivers\DriverInterface;
use Analogue\ORM\Drivers\DBAdapter;

class MongoDriver implements DriverInterface {

    /**
     * The Illuminate Connection Provider
     * 
     * @var CapsuleConnectionProvider | IlluminateConnectionProvider 
     */
    protected $connectionProvider;

    public function __construct($connectionProvider)
    {
        $this->connectionProvider = $connectionProvider;
    }

    /**
     * Return the name of the driver
     * 
     * @return string
     */
    public function getName() : string
    {
        return 'mongodb';
    }

    /**
     * Get Analogue DBAdapter
     * 
     * @param  string $connection 
     * @return \Analogue\ORM\DBAdater
     */
    public function getAdapter(string $connection = null) : DBAdapter
    {
        $connection = $this->connectionProvider->connection($connection);

        return new MongoDBAdapter($connection);
    }


}