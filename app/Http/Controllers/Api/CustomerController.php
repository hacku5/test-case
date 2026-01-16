<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\DTOs\Customer\CreateCustomerDTO;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {
    }

    /**
     * GET /api/customers
     * Tüm müşterileri listele
     */
    public function index(): JsonResponse
    {
        $customers = $this->customerService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Müşteriler başarıyla getirildi.',
            'data' => CustomerResource::collection($customers),
            'meta' => [
                'total' => $customers->count(),
            ],
        ]);
    }

    /**
     * POST /api/customers
     * Yeni müşteri oluştur
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $dto = CreateCustomerDTO::fromRequest($request);

        $customer = $this->customerService->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Müşteri başarıyla oluşturuldu.',
            'data' => new CustomerResource($customer),
        ], 201);
    }
}
