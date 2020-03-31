<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\FeatureDetection;

if (FeatureDetection::softDeletesTraitExists()) {
    // Setup this trait to use the SoftDeletes trait.
    trait SoftDeleteTrait
    {
        use SoftCountTrait, SoftDeletes;
    }
} elseif (FeatureDetection::softDeletingTraitExists()) {
    // Setup this trait to use the SoftDeletingTrait trait.
    trait SoftDeleteTrait
    {
        use SoftCountTrait, SoftDeletingTrait;
    }
} else {
    // Since there are no soft delete traits, this trait won't include any.
    trait SoftDeleteTrait
    {
        use SoftCountTrait;
    }
}
