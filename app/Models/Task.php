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
        'package_type',
        'creator_acc_id',
        'assignee_acc_id',
        'title',
        'description',
        'status',
        'creator_name',
        'assignee_name',
        'start_date',
        'confirm_by_assignee'
    ];
    public function getByArtist()
    {
        return $this->hasOne(Account::class, 'id', 'assignee_acc_id');
    }

    public function getByClient()
    {
        return $this->hasOne(Account::class, 'id', 'creator_acc_id');
    }
}
