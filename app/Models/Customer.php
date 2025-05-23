<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['Customer_name','Customer_address','Customer_phone']; 

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
