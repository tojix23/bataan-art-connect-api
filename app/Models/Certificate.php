<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $table = 'certificates';
    protected $fillable = [
        'acc_id',
        'file_path'
    ];

    // protected $hidden = [
    //     'password', // Hide the password in JSON responses
    //     'created_at', // Optionally hide timestamps
    //     'updated_at', // Optionally hide timestamps
    // ];
}
