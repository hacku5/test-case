<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'name',
        'email',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFullNameAttribute(): string
    {
        return ucwords($this->name);
    }
}
