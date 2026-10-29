<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'objective',
        'duration_weeks',
        'is_recurring',
        'frequency',
        'price',
        'diet_id',
        'advice_id',
        'duration',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'diet_id' => 'integer',
        'advice_id' => 'integer',
        'user_id' => 'integer',
        'duration_weeks' => 'integer',
        'is_recurring' => 'boolean',
    ];

    public function days()
    {
        return $this->hasMany(ProgramDay::class);
    }

    public function diet()
    {
        return $this->belongsTo(Diet::class);
    }

    public function advice()
    {
        return $this->belongsTo(Advice::class);
    }
}
