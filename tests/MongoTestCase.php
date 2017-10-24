<?php

use Illuminate\Contracts\Http\Kernel;
use Analogue\Factory\Factory;
use Faker\Factory as Faker;
use Laravel\BrowserKitTesting\TestCase;
use Laravel\BrowserKitTesting\Concerns\InteractsWithDatabase;

abstract class MongoTestCase extends TestCase
{
    use InteractsWithDatabase;

    protected $analogue;

    protected $usedIds = [];

    public function setUp()
    {
        parent::setUp();

        $this->app->make('config')->set('database.default', 'mongodb');
        $this->app->make('config')->set('database.connections', [
            'mongodb' => [
                'driver'   => 'mongodb',
                'host'     => 'localhost',
                'port'     => 27017,
                'database' => 'analogue_mongodb_test',
                'username' => '',
                'password' => '',
                'options' => [
                    'database' => 'admin' // sets the authentication database required by mongo 3
                ]
            ],
        ]);

        $this->app->singleton(Factory::class, function ($app) {
            $faker = Faker::create();
            $analogueManager = $app->make('analogue');
            return Factory::construct($faker, __DIR__.'/factories', $analogueManager);
        });

        $this->analogue = $this->app->make('analogue');
        $this->analogue->setStrictMode(true);

    }

    protected function resetDatabase()
    {
        DB::getMongoClient()->dropDatabase('analogue_mongodb_test');
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        
        $app->register(\Analogue\ORM\AnalogueServiceProvider::class);
        $app->register(\Analogue\MongoDB\AnalogueMongoServiceProvider::class);
        $app->register(\Analogue\Factory\FactoryServiceProvider::class);
        
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        
        return $app;
    }

    /**
     * Get the mapper for a specific entity
     * 
     * @param  mixed $entity
     * @return Mapper
     */
    protected function mapper($entity) 
    {
        return $this->analogue->mapper($entity);
    }

    /**
     * Get the factory for a specific entity
     * 
     * @param  mixed $entityClass 
     * @return FactoryBuilder
     */
    protected function factory($entityClass) 
    {
        return analogue_factory($entityClass);
    }

    /**
     * Build an entity object
     * 
     * @param  string $entityClass 
     * @param  array  $attributes  
     * @return mixed  
     */
    protected function factoryMake($entityClass, array $attributes = [] )
    {
        return $this->factory($entityClass)->make($attributes);
    }   

     /**
     * Build an entity object with a random ID
     * 
     * @param  string $entityClass 
     * @param  array  $attributes  
     * @return mixed  
     */
    protected function factoryMakeUid($entityClass, array $attributes = [] )
    {
        $attributes['id'] = $this->randId();
        return $this->factory($entityClass)->make($attributes);
    }   

    /**
     * Create an entity object and persist it in database
     * 
     * @param  string $entityClass 
     * @param  array  $attributes  
     * @return mixed  
     */
    protected function factoryCreate($entityClass, array $attributes = [] )
    {
        return $this->factory($entityClass)->create($attributes);
    }

    /**
     * Create an entity object with a random ID and persist it in database
     * 
     * @param  string $entityClass 
     * @param  array  $attributes  
     * @return mixed
     */
    protected function factoryCreateUid($entityClass, array $attributes = [] )
    {
        $attributes['id'] = $this->randId();
        return $this->factory($entityClass)->create($attributes);
    }

    /**
     * Return Faker Factory
     * 
     * @return Faker
     */
    protected function faker()
    {
        return Faker::create();
    }

    /**
     * Generate a random integer
     * 
     * @return integer
     */
    protected function randId()
    {
        do {
            $id = mt_rand(1,10000000);
        }
        while(in_array($id, $this->usedIds));

        return $id;
    }

    /**
     * Run a raw insert statement
     * 
     * @param  string $table
     * @param  array  $columns
     * @return integer
     */
    protected function rawInsert($table, array $columns)
    {
        return DB::table($table)->insertGetId($columns);
    }

    /**
     * Dump events, for debugging purpose.
     *
     * @return void
     */
    protected function logEvents()
    {
        $events = ['store',
        'stored',
        'creating',
        'created',
        'updating',
        'updated',
        'deleting',
        'deleted',
        ];

        foreach ($events as $event) {
            $this->analogue->registerGlobalEvent($event, function ($entity) use ($event) {
                dump(get_class($entity).' '.$event);
            });
        }
    }

    /**
     * Log all queries.
     *
     * @return void
     */
    protected function logQueries()
    {
        $db = $this->db();
        $db->connection('mongodb')->enableQueryLog();
        $db->listen(function ($query) {
            dump($query->sql);
        });
    }
    
    protected function clearCache()
    {
        app('analogue')->clearCache();
    }

    /**
     * Shortcut to db
     * 
     * @return Illuminate\Database\Manager
     */
    protected function db()
    {
        return $this->app->make('db');
    }


    public function tearDown()
    {
        $this->resetDatabase();
        parent::tearDown();
    }
}   
