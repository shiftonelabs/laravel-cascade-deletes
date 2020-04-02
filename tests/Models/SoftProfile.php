<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

class SoftProfile extends Profile
{
    use SoftDeleteTrait;

    // Property defined for Laravel 4.1. Should be harmless for later versions.
    protected $softDelete = true;

    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
