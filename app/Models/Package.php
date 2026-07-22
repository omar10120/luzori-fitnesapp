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
    public function diet()
    {
        return $this->belongsTo(Diet::class); 
    }
    public function advice()
    {
        return $this->belongsTo(Advice::class); 
    }
    public function exercise()
    {
        return $this->belongsTo(Exercise::class); 
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');  
    }

    public function users() {
        return $this->belongsToMany(User::class, 'assign_package_user');
    }

    public function originalExercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    // The package‑specific copy (will exist for every package that has an exercise)
    public function packageExercise()
    {
        return $this->hasOne(PackageExercise::class);
    }

    // Convenience accessor: returns the customised exercise data if available,
    // otherwise falls back to the original.
    public function getEffectiveExerciseAttribute()
    {
        return $this->packageExercise ?? $this->originalExercise;
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
