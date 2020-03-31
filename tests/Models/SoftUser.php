<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

class SoftUser extends User
{
    use SoftDeleteTrait;

    // Property defined for Laravel 4.1. Should be harmless for later versions.
    protected $softDelete = true;

    protected $table = 'users';

    public function friends()
    {
        return $this->belongsToMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftUser', 'friends', 'user_id', 'friend_id');
    }

    public function posts()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftPost', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment', 'user_id');
    }

    public function profile()
    {
        return $this->hasOne('\ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftProfile', 'user_id');
    }
}
