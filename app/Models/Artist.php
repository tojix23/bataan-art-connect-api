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
        'price_range_max',
        'price_range_min',
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

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'id', 'personal_id'); // Assuming email is the linking field
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'acc_id', 'acc_id'); // Assuming email is the linking field
    }
}
