<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrawnNumbers extends Model
{
    protected $table = 'drawn_numbers';

    protected $fillable = [
        'number'
    ];
}
