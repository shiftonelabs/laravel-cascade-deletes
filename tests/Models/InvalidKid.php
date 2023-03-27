<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class InvalidKid extends Model
{
    use CascadesDeletes;

    protected $guarded = [];

    protected $cascadeDeletes = ['invalidable'];

    public function invalidable()
    {
        return $this->morphTo();
    }
}
