<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    protected $guarded = [];

    public function plates()
    {
        return $this->belongsToMany(Plate::class, 'menus_plates');
    }
}
