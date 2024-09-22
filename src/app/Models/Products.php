<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Orders;

class Products extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name',
        'price',
        'sold',
        'stock',
        'created_at',
        'updated_at',
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'products_id');
    }
}
