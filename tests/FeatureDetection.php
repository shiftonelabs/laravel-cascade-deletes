<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FeatureDetection
{
    /**
     * Determine if automatic trait booting is enabled.
     *
     * Model trait booting was added to Laravel 4.2.
     *
     * @return bool
     */
    public static function modelBootsTraits()
    {
        return property_exists(Model::class, 'bootTraits');
    }

    /**
     * Determine if soft deletes is implemented as a property.
     *
     * Soft deleting is implemented as a property in Laravel 4.1. Laravel 4.2
     * moved to using traits and scopes.
     *
     * @return bool
     */
    public static function softDeleteAsProperty()
    {
        return property_exists(Model::class, 'softDelete') && !class_exists(SoftDeletingScope::class);
    }

    /**
     * Determine if the SoftDeletingTrait trait exists.
     *
     * This trait is used to implement soft deleting in Laravel 4.2.
     *
     * @return bool
     */
    public static function softDeletingTraitExists()
    {
        return trait_exists(SoftDeletingTrait::class);
    }

    /**
     * Determine if the SoftDeletes trait exists.
     *
     * This trait is used to implement soft deleting in Laravel 5+.
     *
     * @return bool
     */
    public static function softDeletesTraitExists()
    {
        return trait_exists(SoftDeletes::class);
    }

    /**
     * Determine if removed scopes are handled gracefully.
     *
     * Gracefully handling removed scopes was added in Laravel 5.2.
     *
     * @return bool
     */
    public static function handlesRemovedScopes()
    {
        return method_exists(Builder::class, 'removedScopes');
    }
}
