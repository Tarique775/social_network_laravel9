<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Comment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

//    public function post(){
//        return $this->belongsTo(Post::class);
//    }
    protected $fillable = [
        'post_id',
        'user_id',
        'message',
    ];

    public function replieComments(){
        return $this->hasMany(ReplieComment::class);
    }
}
