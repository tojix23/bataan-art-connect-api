<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';
    protected $fillable = [
        'acc_id',
        'rated_by',
        'rated_for',
        'rating_value',
        'comment',
    ];

    public static function getAverageRating($userId)
    {
        return self::where('acc_id', $userId)->avg('rating_value') ?? 0;
    }
}
