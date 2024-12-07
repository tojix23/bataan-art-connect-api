<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Connection extends Model
{
    use HasFactory;
    protected $table = 'connections';
    protected $fillable = [
        'acc_id',
        'connected_id',
        'status',
    ];

    public function UserInfo()
    {
        return $this->hasOne(Account::class, 'id', 'connected_id'); // Assuming email is the linking field
    }

    public function UserInfoClient()
    {
        return $this->hasOne(Account::class, 'id', 'acc_id'); // Assuming email is the linking field
    }
}
