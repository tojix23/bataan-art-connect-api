<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\MessageReply;

class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = [
        'acc_id',
        'sender_id',
        'reciever_id',
        'content',
        'is_read'
    ];

    public function accountInfo()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id'); // Assuming email is the linking field
    }

    public function replies()
    {
        return $this->hasMany(MessageReply::class, 'message_id', 'id');
    }

    public function sender()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id');
    }

    public function reciever()
    {
        return $this->hasOne(Account::class, 'id', 'reciever_id');
    }
}
