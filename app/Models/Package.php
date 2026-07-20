<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable=[ 'name', 'duration_unit', 'duration', 'price', 'description', 'status', 'diet_id', 'advice_id', 'exercise_id', 'follow_up_price', 'food_recognition_limit' ];
    
    protected $casts = [
        'duration'      => 'integer',
        'price'         => 'double',
        'follow_up_price' => 'double',
        'food_recognition_limit' => 'integer',
        'diet_id' => 'integer',
        'advice_id' => 'integer',
        'exercise_id' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');  
    }
}
