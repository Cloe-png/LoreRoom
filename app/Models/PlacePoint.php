<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacePoint extends Model
{
    protected $table = 'place_points';
    protected $fillable = ['name', 'description', 'position'];
    public function place() { return $this->belongsTo(Place::class); }
}
