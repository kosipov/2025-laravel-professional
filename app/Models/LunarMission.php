<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LunarMission extends Model
{
    protected $casts = [
        'missions' => 'array'
    ];
}
