<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImagePost;
use App\Models\Account;

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

    public function UserInfo()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id'); // Assuming email is the linking field
    }
}
