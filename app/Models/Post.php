<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImagePost;
use App\Models\Account;
use App\Models\VideoPost;
use App\Models\ProfilePhoto;
use App\Models\LikePost;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $appends = ['like_count', 'dislike_count'];
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

    public function UserInfo()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id'); // Assuming email is the linking field
    }

    public function videos()
    {
        return $this->hasMany(VideoPost::class, 'post_id');
    }

    public function ProfilePhoto()
    {
        return $this->hasMany(ProfilePhoto::class, 'acc_id', 'acc_id');
    }

    // public function postLike()
    // {
    //     return $this->hasMany(LikePost::class, 'post_id', 'id');
    // }
    public function postLike()
    {
        return $this->hasMany(LikePost::class, 'post_id', 'id');
    }

    public function currentUserPostLike($accId = null)
    {
        return $this->hasOne(LikePost::class, 'post_id', 'id')
            ->when($accId, function ($query) use ($accId) {
                $query->where('acc_id', $accId);
            });
    }

    public function getLikeCountAttribute()
    {
        return $this->postLike()->where('action', 'like')->count();
    }

    // Accessor to calculate total dislikes
    public function getDislikeCountAttribute()
    {
        return $this->postLike()->where('action', 'dislike')->count();
    }
}
