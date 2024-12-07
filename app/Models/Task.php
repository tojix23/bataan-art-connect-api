<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = [
        'creator_acc_id',
        'assignee_acc_id',
        'title',
        'description',
        'status',
        'creator_name',
        'assignee_name'
    ];
    public function getByArtist()
    {
        return $this->hasOne(Account::class, 'id', 'assignee_acc_id'); // Assuming email is the linking field
    }

    public function getByClient()
    {
        return $this->hasOne(Account::class, 'id', 'creator_acc_id'); // Assuming email is the linking field
    }
}
