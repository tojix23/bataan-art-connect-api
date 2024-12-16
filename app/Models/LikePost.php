<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikePost extends Model
{
    use HasFactory;
    protected $table = 'like_posts';
    protected $fillable = [
        'post_id',
        'acc_id',
        'action'
    ];
}
