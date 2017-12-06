<?php

use Tests\Foo;
use Analogue\MongoDB\EntityMap;

class CacheTest extends MongoTestCase
{
    /** @test */
    public function all_attributes_are_cached()
    {
        $this->analogue->register(Foo::class, new class() extends EntityMap {
            public $timestamps = true;
        });

        $foo = new Foo;
        $foo->name = "test";

        $mapper = mapper(Foo::class);
        $mapper->store($foo);
        
        $this->clearCache();

        $loadedFoo = $mapper->find($foo->_id);

        $this->assertEquals(
            $loadedFoo->getEntityAttributes(),
            $mapper->getEntityCache()->get($foo->_id)
        );
    }
}
