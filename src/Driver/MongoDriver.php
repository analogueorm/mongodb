<?php namespace Analogue\MongoDB\Driver;

use Analogue\ORM\Drivers\DriverInterface;

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
    public function getName()
    {
        return 'mongodb';
    }

    /**
     * Get Analogue DBAdapter
     * 
     * @param  string $connection 
     * @return \Analogue\ORM\DBAdater
     */
    public function getAdapter($connection = null)
    {
        $connection = $this->connectionProvider->connection($connection);

        return new MongoDBAdapter($connection);
    }


}