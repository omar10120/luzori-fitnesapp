<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['name', 'price', 'diet_id', 'advice_id', 'duration', 'status'];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'diet_id' => 'integer',
        'advice_id' => 'integer',
    ];

    public function diet()
    {
        return $this->belongsTo(Diet::class);
    }

    public function advice()
    {
        return $this->belongsTo(Advice::class);
    }
}
