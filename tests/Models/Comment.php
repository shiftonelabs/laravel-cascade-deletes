<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\User');
    }

    public function post()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Post');
    }
}
