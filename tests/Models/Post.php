<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Post extends Model
{
    use CascadesDeletes;

    protected $guarded = [];

    protected $cascadeDeletes = ['photos', 'childPosts', 'comments', 'invalidKids'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public function childPosts()
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function parentPost()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function invalidKids()
    {
        return $this->morphMany(InvalidKid::class, 'invalidable');
    }
}
