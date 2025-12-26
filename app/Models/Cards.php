<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
   protected $fillable = ['user_id', 'numbers'];

    protected $casts = [
        'numbers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
