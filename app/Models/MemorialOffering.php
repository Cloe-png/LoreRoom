<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorialOffering extends Model
{
    protected $fillable = ['user_id', 'offered_on'];
    protected $casts = ['offered_on' => 'date'];
}
