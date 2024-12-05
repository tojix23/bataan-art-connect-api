<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

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

    public function account()
    {
        return $this->belongsTo(Account::class, 'acc_id', 'personal_id');
    }
    public function profilePhoto()
    {
        return $this->hasOne(ProfilePhoto::class, 'acc_id', 'acc_id'); // 'acc_id' links the two tables
    }
}
