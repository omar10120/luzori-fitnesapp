<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageExercise extends Model
{
    protected $fillable = [
        'package_id',
        'exercise_id',
        'title',
        'instruction',
        'tips',
        'video_type',
        'video_url',
        'bodypart_ids',
        'duration',
        'based',
        'type',
        'equipment_id',
        'level_id',
        'sets',
        'status',
        'is_premium',
        'seconds_per_rep',
    ];

    protected $casts = [
        'package_id'       => 'integer',
        'exercise_id'      => 'integer',
        'equipment_id'     => 'integer',
        'level_id'         => 'integer',
        'is_premium'       => 'integer',
        'seconds_per_rep'  => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function originalExercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function getBodypartIdsAttribute($value)
    {
        return isset($value) ? json_decode($value, true) : null;
    }

    public function setBodypartIdsAttribute($value)
    {
        $this->attributes['bodypart_ids'] = isset($value) ? json_encode($value) : null;
    }

    public function getSetsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    public function setSetsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['sets'] = $value;
            return;
        }

        $this->attributes['sets'] = isset($value) ? json_encode(array_values($value)) : null;
    }
}
