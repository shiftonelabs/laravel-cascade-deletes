<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

class SoftPost extends Post
{
    use SoftDeleteTrait;

    // Property defined for Laravel 4.1. Should be harmless for later versions.
    protected $softDelete = true;

    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo(SoftUser::class);
    }

    public function childPosts()
    {
        return $this->hasMany(SoftPost::class, 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo(SoftPost::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
