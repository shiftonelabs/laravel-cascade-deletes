<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftProfile extends Profile
{
    use SoftDeletes;

    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
