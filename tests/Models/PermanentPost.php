<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

class PermanentPost extends Post
{
    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\User');
    }

    public function childPosts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\PermanentPost', 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\PermanentPost', 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment', 'post_id');
    }

    public function delete()
    {
        return false;
    }
}
