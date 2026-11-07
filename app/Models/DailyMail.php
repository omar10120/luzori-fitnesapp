<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'time', 'status'];

    public function diets()
    {
        // Many-to-Many relationship using the pivot table
        return $this->belongsToMany(Diet::class, 'daily_mail_diet');
    }
}