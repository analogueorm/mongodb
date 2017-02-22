<?php

namespace Analogue\MongoDB;

use Analogue\ORM\System\Manager;
use Analogue\ORM\EntityMap as SqlEntityMap;
use Analogue\ORM\Relationships\EmbedsOne;
use Analogue\ORM\Relationships\EmbedsMany;
use Analogue\MongoDB\Relations\BelongsToMany;

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


    /**
     * Define a many-to-many relationship.
     *
     * @param mixed       $entity
     * @param string      $relatedClass
     * @param string|null $collection
     * @param string|null $foreignKey
     * @param string|null $otherKey
     * @param string|null $relation
     *
     * @return \Analogue\MongoDB\Relations\BelongsToMany
     */
    public function belongsToMany($entity, $related, $collection = null, $foreignKey = null, $otherKey = null)
    {
        // Add the relation to the definition in map
        list(, $caller) = debug_backtrace(false);
        $relation = $caller['function'];

        $this->relatedClasses[$relation] = $related;

        $this->addManyRelation($relation);
        $this->addLocalRelation($relation);
        //$this->addPivotRelation($relation);

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $foreignKey = $foreignKey ?: $this->getForeignKey().'s';

        $relatedMapper = Manager::getInstance()->mapper($related);

        $relatedMap = $relatedMapper->getEntityMap();

        $otherKey = $otherKey ?: $relatedMap->getForeignKey().'s';

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($collection)) {
            $collection = $relatedMap->getTable();
        }

        return new BelongsToMany($relatedMapper, $entity, $collection, $foreignKey, $otherKey, $relation);
    }
}