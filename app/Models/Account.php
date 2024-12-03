<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Import the trait
class Account extends Model
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
}
