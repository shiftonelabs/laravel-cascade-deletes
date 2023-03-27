<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftPost extends Post
{
    use SoftDeletes;

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
