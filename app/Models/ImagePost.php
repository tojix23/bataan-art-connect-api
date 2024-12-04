<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class ImagePost extends Model
{
    use HasFactory;
    protected $table = 'image_posts';
    protected $fillable = [
        'post_id',
        'image_path',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
