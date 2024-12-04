<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\ProfilePhoto;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments';
    protected $fillable = [
        'post_id',
        'acc_id',
        'comment',
        'is_remove'
    ];

    public function UserInfo()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id'); // Assuming email is the linking field
    }
    public function profilePhoto()
    {
        return $this->hasOne(ProfilePhoto::class, 'acc_id', 'acc_id'); // Assuming email is the linking field
    }
}
