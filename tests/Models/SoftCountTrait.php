<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Throwable;

trait SoftCountTrait
{
    /**
     * Explicit static count function to count soft deleted records.
     *
     * Soft delete implementations in Laravel <= 5.3 throw exceptions in
     * PHP >= 7.2. This explicit static count function will shortcut
     * the magic static count function to workaround the issues.
     *
     * @return int
     *
     * @throws \Throwable
     */
    public static function count()
    {
        $model = new static();

        try {
            return $model->newQuery()->count();
        } catch (Throwable $e) {
            if (stripos($e->getMessage(), 'count():') === 0) {
                return $model->newQueryWithoutScopes()->whereNull($model->getQualifiedDeletedAtColumn())->count();
            }

            throw $e;
        }
    }
}
