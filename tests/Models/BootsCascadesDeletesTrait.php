<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use ShiftOneLabs\LaravelCascadeDeletes\Tests\FeatureDetection;

if (FeatureDetection::modelBootsTraits()) {
    // Since the model boots traits, this trait doesn't need to do anything.
    trait BootsCascadesDeletesTrait
    {
        //
    }
} else {
    // Since the model doesn't automatically boot traits, this trait needs to
    // override the static boot method in order to boot the custom trait.
    trait BootsCascadesDeletesTrait
    {
        protected static function boot()
        {
            parent::boot();

            static::bootCascadesDeletes();
        }
    }
}
