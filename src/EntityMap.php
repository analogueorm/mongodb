<?php

namespace Analogue\MongoDB;

use Analogue\ORM\EntityMap as SqlEntityMap;
use Analogue\ORM\Relationships\EmbedsOne;
use Analogue\ORM\Relationships\EmbedsMany;

class EntityMap extends SqlEntityMap {

    /**
     * The driver used for this entity
     * 
     * @var string
     */
    protected $driver = 'mongodb';

    /**
     * The primary key for the entity.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * The sequence used for this entity
     * 
     * @var string | null
     */
    protected $sequence = null;

    /**
     * Get the sequence name
     *
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Define an Embedded Object.
     *
     * @param mixed  $entity
     * @param string $related
     *
     * @return EmbedsOne
     */
    public function embedsOne($parent, string $relatedClass, $relation = null) : EmbedsOne
    {
        if(is_null($relation)) {
            list(, $caller) = debug_backtrace(false);
            $relation = $caller['function'];
        }

        $relationship = parent::embedsOne($parent, $relatedClass, $relation);

        return $relationship->asArray();
    }

    /**
     * Define an Embedded Collection.
     *
     * @param mixed  $entity
     * @param string $related
     *
     * @return EmbedsOne
     */
    public function embedsMany($parent, string $relatedClass, $relation = null) : EmbedsMany
    {
        if(is_null($relation)) {
            list(, $caller) = debug_backtrace(false);
            $relation = $caller['function'];
        }

        $relationship = parent::embedsMany($parent, $relatedClass, $relation);

        return $relationship->asArray();
    }


}