<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class VideoPost extends Model
{
    use HasFactory;
    protected $table = 'video_posts';
    protected $fillable = [
        'post_id',
        'video_path',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
