<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Advice extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'seed_text', 'status'];

    public function options()
    {
        return $this->belongsToMany(AdviceOption::class, 'advice_option_advice')
                    ->withPivot('is_required')
                    ->withTimestamps();
    }


    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}