<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;
    protected $table = 'personal_infos';
    protected $fillable = [
        'first_name',
        'last_name',
        'main_address',
        'sub_address',
        'occupation',
        'role',
        'gender',
        'contact_number',
        'birthdate',
        'type',
        'email',
        'bio'
    ];
    protected $hidden = [
        // 'password', // Hide the password in JSON responses
        'created_at', // Optionally hide timestamps
        'updated_at', // Optionally hide timestamps
    ];
    // protected $casts = [
    //     'birthdate' => 'date', // Cast birthdate to a date object
    // ];
    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'email', 'email'); // Assuming email is the linking field
    }
}
