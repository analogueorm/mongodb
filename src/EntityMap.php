<?php

namespace Analogue\MongoDB;

use Analogue\ORM\EntityMap as SqlEntityMap;

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
}