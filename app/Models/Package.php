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

    public function users() {
        return $this->belongsToMany(User::class, 'assign_package_user');
    }

    /**
     * Packages with no assigned users are public.
     * Packages with assigned users are visible only to those users.
     */
    public function scopeAvailableForUser($query, $userId = null)
    {
        $userId = $userId ?? auth('sanctum')->id();

        return $query->where(function ($q) use ($userId) {
            $q->whereDoesntHave('users');

            if ($userId) {
                $q->orWhereHas('users', function ($users) use ($userId) {
                    $users->where('users.id', $userId);
                });
            }
        });
    }
}
