<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingAttachment extends Model
{
    use HasFactory;
    protected $table = 'rating_attachments';
    protected $fillable = [
        'rating_id',
        'rate_by',
        'file_path',
    ];
}
