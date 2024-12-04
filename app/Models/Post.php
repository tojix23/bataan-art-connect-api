<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImagePost;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
        'acc_id',
        'description',
        'is_approved',
        'like'
    ];

    public function images()
    {
        return $this->hasMany(ImagePost::class, 'post_id');
    }
}
