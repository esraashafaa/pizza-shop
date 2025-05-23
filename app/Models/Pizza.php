<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    protected $fillable = ['name', 'price' ,'description','available'];

    public function toppings()
    {
        return $this->belongsToMany(Topping::class);
    }
}
