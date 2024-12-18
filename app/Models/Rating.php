<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RatingAttachment;

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

    public function attachment()
    {
        return $this->hasOne(RatingAttachment::class, 'rating_id', 'id'); // Assuming email is the linking field
    }
}
