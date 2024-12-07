<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class MessageReply extends Model
{
    use HasFactory;
    protected $table = 'message_replies';
    protected $appends = ['name'];
    protected $fillable = [
        'message_id',
        'sender_id',
        'reciever_id',
        'content'
    ];
    public function personalInfo()
    {
        return $this->hasOne(Account::class, 'id', 'sender_id'); // Assuming email is the linking field
    }
    public function getNameAttribute()
    {
        return $this->personalInfo?->fullname; // Use the relationship to fetch 'role'
    }
}
