<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodAnalysisUsage extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'used',
        'daily_limit',
    ];

    protected $casts = [
        'user_id'     => 'integer',
        'date'        => 'date',
        'used'        => 'integer',
        'daily_limit' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
