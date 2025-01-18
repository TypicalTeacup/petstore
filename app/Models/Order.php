<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $dates = ['created_at', 'updated_at', 'ship_date'];

    protected $fillable = [
        'pet_id',
        'quantity',
        'ship_date',
        'complete',
        'status',
    ];
}
