<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Import the trait
use App\Models\PersonalInfo;
use App\Models\ProfilePhoto;
use App\Models\Certificate;
use App\Models\Artist;
use App\Models\ClientID;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $table = 'accounts';
    protected $appends = ['role'];
    protected $fillable = [
        'personal_id',
        'fullname',
        'password',
        'type',
        'email_verified_at',
        'email',
        'is_disable',
        'is_cancel',
        'is_verify'
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
    // Define the accessor for 'role'
    public function getRoleAttribute()
    {
        return $this->personalInfo?->role; // Use the relationship to fetch 'role'
    }
    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'acc_id', 'id'); // Assuming email is the linking field
    }

    public function artists()
    {
        return $this->hasMany(Artist::class, 'acc_id', 'personal_id');
    }

    public function valid_id()
    {
        return $this->hasOne(ClientID::class, 'acc_id', 'id'); // Assuming email is the linking field
    }
}
