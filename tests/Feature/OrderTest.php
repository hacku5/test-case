<?php

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Test her çalıştığında veritabanını sıfırla (RefreshDatabase)
uses(RefreshDatabase::class);

test('can create an order successfully and reduces stock', function () {
    // 1. HAZIRLIK (Arrange)
    // Bir müşteri ve stokta 10 tane olan bir ürün oluştur
    $customer = Customer::factory()->create();
    $product = Product::factory()->create([
        'price' => 20000, // 200 TL
        'stock_quantity' => 10
    ]);

    // API'ye göndereceğimiz veri paketi
    $payload = [
        'customer_id' => $customer->id,
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2 // 2 tane alalım
            ]
        ]
    ];

    // 2. EYLEM (Act)
    // Endpoint'e POST isteği at
    $response = $this->postJson('/api/orders', $payload);

    // 3. DOĞRULAMA (Assert)

    // A) HTTP Yanıtı kontrolü
    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'total_amount', 'status']
        ]);

    // B) Veritabanı kontrolü (Sipariş oluştu mu?)
    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'total_amount' => 40000, // 200 * 2 = 400 TL (40000 kuruş)
    ]);

    // C) Kritik Stok Kontrolü (Business Logic Testi)
    // Veritabanından ürünü taze haliyle tekrar çek
    $product->refresh();

    // Stok 10'du, 2 satıldı, 8 kalmalı.
    expect($product->stock_quantity)->toBe(8);
});

test('cannot order more than available stock', function () {
    // Stokta sadece 1 tane var
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 1]);

    $payload = [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5] // 5 tane istiyoruz
        ]
    ];

    $response = $this->postJson('/api/orders', $payload);

    // 400 Hatası bekliyoruz (Serviste Exception fırlatmıştık)
    $response->assertStatus(400);

    // Stok değişmemeli (Transaction Rollback kontrolü)
    $product->refresh();
    expect($product->stock_quantity)->toBe(1);
});