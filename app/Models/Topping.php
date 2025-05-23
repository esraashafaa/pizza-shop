<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    protected $fillable = ['name', 'price' ,'available'];

    public function toppings()
    {
        return $this->belongsToMany(Pizza::class);
    }
    public function orderPizzas() {
        return $this->belongsToMany(OrderPizza::class, 'order_pizza_topping');
    }
}
