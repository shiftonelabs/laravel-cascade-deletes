<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class User extends Model
{
    use CascadesDeletes;

    protected $guarded = [];

    protected $cascadeDeletes = ['friends', 'posts', 'photos', 'comments', 'profile'];

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function permanentPosts()
    {
        return $this->hasMany(PermanentPost::class);
    }
}
