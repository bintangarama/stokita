<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
