<?php

namespace ShiftOneLabs\LaravelCascadeDeletes;

use LogicException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CascadesDeletes
{
    /**
     * Use the boot function to setup model event listeners.
     *
     * @return void
     */
    public static function bootCascadesDeletes()
    {
        // Setup the 'deleting' event listener.
        static::deleting(function ($model) {

            // Wrap all of the cascading deletes inside of a transaction to make this an
            // all or nothing operation. Any exceptions thrown inside the transaction
            // need to bubble up to make sure all transactions will be rolled back.
            $model->getConnectionResolver()->transaction(function () use ($model) {

                $relations = $model->getCascadeDeletesRelations();

                if ($invalidRelations = $model->getInvalidCascadeDeletesRelations($relations)) {
                    throw new LogicException(sprintf('[%s]: invalid relationship(s) for cascading deletes. Relationship method(s) [%s] must return an object of type Illuminate\Database\Eloquent\Relations\Relation.', static::class, implode(', ', $invalidRelations)));
                }

                $deleteMethod = $model->isCascadeDeletesForceDeleting() ? 'forceDelete' : 'delete';

                foreach ($relations as $relationName => $relation) {
                    $expected = 0;
                    $deleted = 0;

                    if ($relation instanceof BelongsToMany) {
                        // Process the many-to-many relationships on the model.
                        // These relationships should not delete the related
                        // record, but should just detach from each other.

                        $expected = $model->getCascadeDeletesRelationQuery($relationName)->count();

                        $deleted = $model->getCascadeDeletesRelationQuery($relationName)->detach();
                    } elseif ($relation instanceof HasOneOrMany) {
                        // Process the one-to-one and one-to-many relationships
                        // on the model. These relationships should actually
                        // delete the related records from the database.

                        $children = $model->getCascadeDeletesRelationQuery($relationName)->get();

                        // To protect against potential relationship defaults,
                        // filter out any children that may not actually be
                        // Model instances, or that don't actually exist.
                        $children = $children->filter(function ($child) {
                            return $child instanceof Model && $child->exists;
                        })->all();

                        $expected = count($children);

                        foreach ($children as $child) {
                            // Delete the record using the proper method.
                            $deleted += $child->$deleteMethod();
                        }
                    } else {
                        // Not all relationship types make sense for cascading. As an
                        // example, for a BelongsTo relationship, it does not make
                        // sense to delete the parent when the child is deleted.
                        throw new LogicException(sprintf('[%s]: error occurred deleting [%s]. Relation type [%s] not handled.', static::class, $relationName, get_class($relation)));
                    }

                    if ($deleted < $expected) {
                        throw new LogicException(sprintf('[%s]: error occurred deleting [%s]. Only deleted [%d] out of [%d] records.', static::class, $relationName, $deleted, $expected));
                    }
                }
            });
        });
    }

    /**
     * Get the value of the cascadeDeletes attribute, if it exists.
     *
     * @return mixed
     */
    public function getCascadeDeletes()
    {
        return property_exists($this, 'cascadeDeletes') ? $this->cascadeDeletes : [];
    }

    /**
     * Set the cascadeDeletes attribute.
     *
     * @param  mixed  $cascadeDeletes
     *
     * @return void
     */
    public function setCascadeDeletes($cascadeDeletes)
    {
        $this->cascadeDeletes = $cascadeDeletes;
    }

    /**
     * Get an array of cascading relation names.
     *
     * @return array
     */
    public function getCascadeDeletesRelationNames()
    {
        $deletes = $this->getCascadeDeletes();

        return array_filter(is_array($deletes) ? $deletes : [$deletes]);
    }

    /**
     * Get an array of the cascading relation names mapped to their relation types.
     *
     * @return array
     */
    public function getCascadeDeletesRelations()
    {
        $names = $this->getCascadeDeletesRelationNames();

        return array_combine($names, array_map(function ($name) {
            $relation = method_exists($this, $name) ? $this->$name() : null;

            return $relation instanceof Relation ? $relation : null;
        }, $names));
    }

    /**
     * Get an array of the invalid cascading relation names.
     *
     * @param  array|null  $relations
     *
     * @return array
     */
    public function getInvalidCascadeDeletesRelations(array $relations = null)
    {
        // This will get the array keys for any item in the array where the
        // value is null. If the value is null, that means that the name
        // of the relation provided does not return a Relation object.
        return array_keys($relations ?: $this->getCascadeDeletesRelations(), null);
    }

    /**
     * Get the relationship query to use for the specified relation.
     *
     * @param  string  $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getCascadeDeletesRelationQuery($relation)
    {
        $query = $this->$relation();

        // If this is a force delete and the related model is using soft deletes,
        // we need to use the withTrashed() scope on the relationship query to
        // ensure all related records, plus soft deleted, are force deleted.
        if ($this->isCascadeDeletesForceDeleting() && !is_null($query->getMacro('withTrashed'))) {
            $query = $query->withTrashed();
        }

        return $query;
    }

    /**
     * Check if this cascading delete is a force delete.
     *
     * @return boolean
     */
    public function isCascadeDeletesForceDeleting()
    {
        return property_exists($this, 'forceDeleting') && $this->forceDeleting;
    }
}
