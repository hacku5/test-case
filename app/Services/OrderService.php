<?php

namespace App\Services;

use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\OrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository
    ) {
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->orderRepository->getWithRelations();
    }

    public function getById(string $id): Order
    {
        return $this->orderRepository->findWithRelations($id);
    }

    public function createOrder(CreateOrderDTO $dto): Order
    {
        if (empty($dto->items)) {
            throw new ServiceException('Sipariş en az 1 ürün içermelidir.');
        }

        Log::info("[Process] Sipariş oluşturma süreci başladı. Müşteri ID: {$dto->customerId}");

        $this->checkDailyOrderLimit($dto->customerId);

        return DB::transaction(function () use ($dto) {
            $totalAmount = 0;
            $orderItemsData = [];

            $order = $this->orderRepository->create([
                'customer_id' => $dto->customerId,
                'total_amount' => 0,
                'status' => OrderStatus::PENDING,
            ]);
            Log::info("[Process] Sipariş kaydı oluşturuldu. ID: {$order->id}");

            foreach ($dto->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock_quantity < $item['quantity']) {
                    Log::warning("[Process] Stok yetersiz! Ürün: {$product->name}, İstenen: {$item['quantity']}, Stok: {$product->stock_quantity}");
                    throw new ServiceException("Ürün stoğu yetersiz: {$product->name}");
                }
                Log::info("[Process] Stok kontrolü başarılı: {$product->name}");

                $lineTotal = $product->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $product->decrement('stock_quantity', $item['quantity']);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }
            Log::info("[Process] Sipariş kalemleri kaydedildi. Adet: " . count($orderItemsData));

            $order->update(['total_amount' => $totalAmount]);

            return $order->load('items');
        });
    }

    public function updateStatus(UpdateOrderStatusDTO $dto): Order
    {
        $order = $this->orderRepository->findOrFail($dto->orderId);

        if ($order->status === OrderStatus::CANCELLED) {
            throw new ServiceException('İptal edilmiş siparişler güncellenemez.');
        }

        if (!$order->status->canTransitionTo($dto->status)) {
            throw new ServiceException(
                sprintf(
                    '%s durumundaki sipariş %s durumuna geçirilemez.',
                    $order->status->label(),
                    $dto->status->label()
                )
            );
        }

        return DB::transaction(function () use ($dto, $order) {
            if ($dto->status === OrderStatus::CANCELLED) {
                $order->load('items');
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
                Log::info("[Process] İptal edilen sipariş için stok iadesi yapıldı. Sipariş ID: {$order->id}");
            }

            return $this->orderRepository->update($dto->orderId, ['status' => $dto->status]);
        });
    }

    private function checkDailyOrderLimit(string $customerId): void
    {
        $todayOrderCount = $this->orderRepository->countTodayOrdersByCustomer($customerId);
        Log::info("[Process] Günlük limit kontrolü yapıldı. Bugün: {$todayOrderCount}/5");

        if ($todayOrderCount >= 5) {
            throw new ServiceException('Aynı gün içinde en fazla 5 sipariş oluşturabilirsiniz.');
        }
    }
}