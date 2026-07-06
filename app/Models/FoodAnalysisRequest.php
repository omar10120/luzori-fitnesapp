<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FoodAnalysisRequest extends Model implements HasMedia
{
    use InteractsWithMedia;

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'provider',
        'is_food',
        'top_food_name',
        'top_group',
        'top_score',
        'calories',
        'protein',
        'total_fat',
        'total_carbs',
        'response_json',
        'status',
    ];

    protected $casts = [
        'user_id'       => 'integer',
        'is_food'       => 'boolean',
        'top_score'     => 'integer',
        'calories'      => 'decimal:4',
        'protein'       => 'decimal:4',
        'total_fat'     => 'decimal:4',
        'total_carbs'   => 'decimal:4',
        'response_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
