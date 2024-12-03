<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Import the trait
use App\Models\PersonalInfo;
use App\Models\ProfilePhoto;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $table = 'accounts';
    protected $fillable = [
        'personal_id',
        'fullname',
        'password',
        'type',
        'email_verified_at',
        'email'
    ];

    protected $hidden = [
        'password', // Hide the password in JSON responses
        'created_at', // Optionally hide timestamps
        'updated_at', // Optionally hide timestamps
    ];

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'email', 'email'); // Assuming email is the linking field
    }
    public function profilePhoto()
    {
        return $this->hasOne(ProfilePhoto::class, 'acc_id', 'id'); // Assuming email is the linking field
    }
}
