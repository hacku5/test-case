<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        "order_id",
        "product_id",
        "quantity",
        "unit_price",
    ];

    protected $casts = [
        "quantity" => "integer",
        "unit_price" => "integer",
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getUnitPriceAttribute($value)
    {
        return $value / 100;
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value * 100;
    }
}
