<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class Orders extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'orders_id',
        'products_id',
        'quantity',
    ];

    public function products()
    {
        return $this->belongsTo(Products::class, 'products_id');
    }
}
