<?php 

namespace Analogue\MongoDB\Relations;

use Analogue\ORM\Relationships\BelongsToMany as AnalogueBelongsToMany;
use Analogue\ORM\EntityCollection;
use Illuminate\Support\Collection;
use Analogue\ORM\Exceptions\MappingException;

class BelongsToMany extends AnalogueBelongsToMany
{
    /**
     * @inheritdoc
     */
    protected function hydratePivotRelation(array $entities)
    {
        return $entities;
    }

    /**
     * Set the select clause for the relation query.
     *
     * @param  array  $columns
     * @return \Analogue\MongoDB\Relations\BelongsToMany
     */
    protected function getSelectColumns(array $columns = ['*'])
    {
        return $columns;
    }

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->setWhere();
        }
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function setWhere()
    {
        /*$foreign = $this->getForeignKey();

        $parentKey = $this->parentMap->getKeyName();

        $this->query->where($foreign, '=', $this->parent->getEntityAttribute($parentKey));*/

        //$foreign = $this->getForeignKey();

        $relatedKey = $this->relatedMap->getKeyName();

        $this->query->whereIn($relatedKey, $this->getLocalKeyValues());

        return $this;
    }

    /**
     * Return local keys
     * 
     * @return array
     */
    protected function getLocalKeyValues() : array
    {
        $keys = $this->parent->getEntityAttribute($this->otherKey);

        return is_null($keys) ? [] : $keys;
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
     *
     * @param  array  $ids
     * @param  bool   $detaching
     * @return array
     */
    public function sync(array $ids)
    {
        /*changes = [
            'attached' => [], 'detached' => [], 'updated' => [],
        ];

        if ($ids instanceof Collection) {
            $ids = $ids->modelKeys();
        }

        // First we need to attach any of the associated models that are not currently
        // in this joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $this->parent->{$this->otherKey} ?: [];

        // See issue #256.
        if ($current instanceof Collection) {
            $current = $ids->modelKeys();
        }

        $records = $this->formatSyncList($ids);

        $detach = array_diff($current, array_keys($records));

        // We need to make sure we pass a clean array, so that it is not interpreted
        // as an associative array.
        $detach = array_values($detach);

        // Next, we will take the differences of the currents and given IDs and detach
        // all of the entities that exist in the "current" array but are not in the
        // the array of the IDs given to the method which will complete the sync.
        if ($detaching and count($detach) > 0) {
            $this->detach($detach);

            $changes['detached'] = (array) array_map(function ($v) {
                return is_numeric($v) ? (int) $v : (string) $v;
            }, $detach);
        }

        // Now we are finally ready to attach the new records. Note that we'll disable
        // touching until after the entire operation is complete so we don't fire a
        // ton of touch operations until we are totally done syncing the records.
        $changes = array_merge(
            $changes, $this->attachNew($records, $current, false)
        );

        if (count($changes['attached']) || count($changes['updated'])) {
            $this->touchIfTouching();
        }

        return $changes;*/
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     */
    public function updateExistingPivot($id, array $attributes)
    {
        // Do nothing, we have no pivot table.
    }

    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     */
    public function attach($id, array $attributes = [])
    {
        /*if ($id instanceof Model) {
            $model = $id;

            $id = $model->getKey();

            // Attach the new parent id to the related model.
            $model->push($this->foreignKey, $this->parent->getKey(), true);
        } else {
            $query = $this->newRelatedQuery();

            $query->whereIn($this->related->getKeyName(), (array) $id);

            // Attach the new parent id to the related model.
            $query->push($this->foreignKey, $this->parent->getKey(), true);
        }

        // Attach the new ids to the parent model.
        $this->parent->push($this->otherKey, (array) $id, true);

        if ($touch) {
            $this->touchIfTouching();
        }*/
    }

    /**
     * Detach models from the relationship.
     *
     * @param  int|array  $ids
     * @return int
     */
    public function detach($ids = [])
    {
        /*if ($ids instanceof Model) {
            $ids = (array) $ids->getKey();
        }

        $query = $this->newRelatedQuery();

        // If associated IDs were passed to the method we will only delete those
        // associations, otherwise all of the association ties will be broken.
        // We'll return the numbers of affected rows when we do the deletes.
        $ids = (array) $ids;

        // Detach all ids from the parent model.
        $this->parent->pull($this->otherKey, $ids);

        // Prepare the query to select all related objects.
        if (count($ids) > 0) {
            $query->whereIn($this->related->getKeyName(), $ids);
        }

        // Remove the relation to the parent.
        $query->pull($this->foreignKey, $this->parent->getKey());

        if ($touch) {
            $this->touchIfTouching();
        }

        return count($ids);*/
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return array
     */
    protected function buildDictionary(EntityCollection $results)
    {
        //dd($results);
        /*$foreign = $this->foreignKey;

        // First we will build a dictionary of child models keyed by the foreign key
        // of the relation so that we will easily and quickly match them to their
        // parents without having a possibly slow inner loops for every models.
        $dictionary = [];

        foreach ($results as $result) {
            foreach ($result->$foreign as $item) {
                $dictionary[$item][] = $result;
            }
        }

        return $dictionary;*/
    }

    /**
     * Create a new query builder for the related model.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newPivotQuery()
    {
        return $this->newRelatedQuery();
    }

    /**
     * Create a new query builder for the related model.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newRelatedQuery()
    {
        return $this->related->newQuery();
    }

    /**
     * Get the fully qualified foreign key for the relation.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Return the column that will be used to locally store
     * the related id(s)
     * 
     * @return array
     */
    public function getForeignKeyValuePair() : array
    {
        $key = $this->otherKey;
        
        $relatedEntities = $this->parent->getEntityAttribute($this->relation);

        if($relatedEntities instanceof Collection) {
            $relatedEntities = $relatedEntities->all(); 
        }

        if(! is_array($relatedEntities)) {
            throw new MappingException("$this->relation should be an array or collection.");
        }

        $relatedKey = $this->relatedMapper->getEntityMap()->getKeyName();
        $host = $this;
        $keys = array_map(function($entity) use ($relatedKey, $host) {
            $wrapper = $host->factory->make($entity);
            return $wrapper->getEntityAttribute($relatedKey);
        }, $relatedEntities);

        return [$key => $keys];
    }
}
