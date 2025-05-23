<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPizza extends Model
{
    protected $fillable = [
        'order_id',
        'pizza_id',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pizza()
    {
        return $this->belongsTo(Pizza::class);
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'order_pizza_topping');
    }
    protected $table = 'order_pizza';

}

