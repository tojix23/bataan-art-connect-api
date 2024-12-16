<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistPackage extends Model
{
    use HasFactory;
    protected $table = 'artist_packages';
    protected $fillable = [
        'acc_id',
        'package_name',
        'package_desc',
        'amount',
        'is_active',
        'image_attach'
    ];
}
