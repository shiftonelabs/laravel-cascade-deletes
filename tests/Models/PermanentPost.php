<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

class PermanentPost extends Post
{
    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function childPosts()
    {
        return $this->hasMany(PermanentPost::class, 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo(PermanentPost::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function delete()
    {
        return false;
    }
}
