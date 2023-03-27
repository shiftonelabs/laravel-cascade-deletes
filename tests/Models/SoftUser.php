<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftUser extends User
{
    use SoftDeletes;

    protected $table = 'users';

    public function friends()
    {
        return $this->belongsToMany(SoftUser::class, 'friends', 'user_id', 'friend_id');
    }

    public function posts()
    {
        return $this->hasMany(SoftPost::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(SoftProfile::class, 'user_id');
    }
}
