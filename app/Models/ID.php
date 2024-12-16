<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ID extends Model
{
    use HasFactory;
    protected $table = 'i_d_s';
    protected $fillable = [
        'acc_id',
        'file_path'
    ];
}
