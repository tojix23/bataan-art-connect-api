<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;
    protected $table = 'connections';
    protected $fillable = [
        'acc_id',
        'connected_id',
        'status',
        'content',
        'is_read'
    ];
}
