<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function allWithRelations(): LengthAwarePaginator
    {
        return $this->model
            ->with(['customer', 'items', 'items.product'])
            ->withCount('items')
            ->latest()
            ->paginate($this->perPage);
    }

    public function getWithRelations(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->with(['customer', 'items', 'items.product'])
            ->withCount('items')
            ->latest()
            ->get();
    }

    public function findWithRelations(string $id): Order
    {
        return $this->model
            ->with(['customer', 'items.product'])
            ->findOrFail($id);
    }

    public function countTodayOrdersByCustomer(string $customerId): int
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->whereDate('created_at', today())
            ->count();
    }

    public function findByCustomer(string $customerId): LengthAwarePaginator
    {
        return $this->model
            ->with(['items.product'])
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate($this->perPage);
    }
}
