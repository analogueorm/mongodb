<?php namespace Analogue\MongoDB;

use Illuminate\Support\ServiceProvider;
use Analogue\MongoDB\Driver\MongoDriver;
use Analogue\ORM\Drivers\IlluminateConnectionProvider;

class AnalogueMongoServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot()
    {
        $db = $this->app['db'];

        $driverManager = $this->app['analogue']->getDriverManager();

        // The driver could be created on demand.
        $connectionProvider = new IlluminateConnectionProvider($db);

        $mongo = new MongoDriver($connectionProvider);

        $driverManager->addDriver($mongo);
        
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Jenssegers\Mongodb\MongodbServiceProvider::class);
    }
    
}
