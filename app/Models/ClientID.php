<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientID extends Model
{
    use HasFactory;
    protected $table = 'client_i_d_s';
    protected $fillable = [
        'acc_id',
        'file_path',
        'id_type'
    ];
}
