<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    /** @use HasFactory<\Database\Factories\PlateFactory> */
    use HasFactory;

    protected $guarded = [];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menus_plates');
    }
}
