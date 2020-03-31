<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletesModel;

class ExtendedUser extends CascadesDeletesModel
{
    use BootsCascadesDeletesTrait;

    protected $guarded = [];

    protected $cascadeDeletes = ['friends', 'posts', 'photos', 'comments', 'profile'];

    public function friends()
    {
        return $this->belongsToMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\User', 'friends', 'user_id', 'friend_id');
    }

    public function posts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Post');
    }

    public function photos()
    {
        return $this->morphMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Photo', 'imageable');
    }

    public function comments()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment');
    }

    public function profile()
    {
        return $this->hasOne('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Profile');
    }

    public function permanentPosts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\PermanentPost');
    }
}
