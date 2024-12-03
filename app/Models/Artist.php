<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $table = 'artists';
    protected $fillable = [
        'personal_id',
        'acc_id',
        'full_name',
        'price_range',
        'occupation',
    ];
}
