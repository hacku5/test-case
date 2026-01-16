<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Sadece aktif ürünleri getir
     */
    public function allActive()
    {
        return $this->model
            ->where('is_active', true)
            ->latest()
            ->paginate($this->perPage);
    }
}
