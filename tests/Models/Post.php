<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Post extends Model
{
    use CascadesDeletes, BootsCascadesDeletesTrait;

    protected $guarded = [];

    protected $cascadeDeletes = ['photos', 'childPosts', 'comments', 'invalidKids'];

    public function user()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\User');
    }

    public function photos()
    {
        return $this->morphMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Photo', 'imageable');
    }

    public function childPosts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Post', 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Post', 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment');
    }

    public function invalidKids()
    {
        return $this->morphMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\InvalidKid', 'invalidable');
    }
}
