<?php

namespace App\Services;

use App\DTOs\Customer\CreateCustomerDTO;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * Tüm müşterileri getir (pagination ile)
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->customerRepository->get();
    }

    /**
     * Yeni müşteri oluştur
     */
    public function create(CreateCustomerDTO $dto): Customer
    {
        return $this->customerRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
        ]);
    }
}
