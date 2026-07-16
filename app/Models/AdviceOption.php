<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdviceOption extends Model
{
    protected $fillable = ['key', 'label', 'description', 'order', 'is_active'];

    public function advices()
    {
        return $this->belongsToMany(Advice::class, 'advice_option_advice');
    }
}