<?php
namespace App\Src\Media;

use App\Src\Comment\Comment;
use App\Src\User\User;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected $table = 'medias';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class,'favorites');
    }

    public function downloads()
    {
        return $this->belongsToMany(User::class,'downloads');
    }

}
