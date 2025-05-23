<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status_id',
        'total_price',
    ];
    public function customer()
{
    return $this->belongsTo(Customer::class);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function pizzas()
    {
        return $this->hasMany(OrderPizza::class);
    }
}

