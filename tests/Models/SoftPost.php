<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftPost extends Post
{
    use SoftDeletes;

    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftUser');
    }

    public function childPosts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftPost', 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftPost', 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment', 'post_id');
    }
}
