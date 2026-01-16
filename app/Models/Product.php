<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
