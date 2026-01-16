<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    /**
     * GET /api/orders
     * Tüm siparişleri listele (pagination ile)
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Siparişler başarıyla getirildi.',
            'data' => OrderResource::collection($orders),
            'meta' => [
                'total' => $orders->count(),
            ],
        ]);
    }

    /**
     * POST /api/orders
     * Yeni sipariş oluştur
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request);

        $order = $this->orderService->createOrder($dto);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla oluşturuldu.',
            'data' => new OrderResource($order->load(['customer', 'items.product'])),
        ], 201);
    }

    /**
     * GET /api/orders/{id}
     * Tek bir siparişi görüntüle
     */
    public function show(string $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla getirildi.',
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * PATCH /api/orders/{id}/status
     * Sipariş durumunu güncelle
     */
    public function updateStatus(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        $dto = UpdateOrderStatusDTO::fromRequest($request, $id);

        $order = $this->orderService->updateStatus($dto);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş durumu başarıyla güncellendi.',
            'data' => new OrderResource($order->load(['customer', 'items.product'])),
        ]);
    }
}