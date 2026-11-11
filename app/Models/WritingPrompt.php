<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingPrompt extends Model
{
    protected $fillable = ['text', 'target_type', 'section_hint', 'is_active'];
}
