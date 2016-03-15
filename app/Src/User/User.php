<?php

namespace App\Src\User;

use App\Src\Comment\Comment;
use App\Src\Media\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token','pivot'];


    /* Model Relations */

    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * People who follow the User
     */
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','followee_id','follower_id');
    }

    /**
     * People The User is Following
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followee_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Media::class,'favorites');
    }

    public function downloads()
    {
        return $this->belongsToMany(Media::class,'downloads');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
