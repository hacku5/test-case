<?php

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================
// Sipariş Oluşturma İş Kuralları
// ============================================

test('sipariş en az 1 ürün içermelidir', function () {
    $customer = Customer::factory()->create();

    $response = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [] // Boş items
    ]);

    // Validation hatası döner (FormRequest)
    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Doğrulama hatası.',
        ]);
});

test('aynı müşteri günde en fazla 5 sipariş oluşturabilir', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 100]);

    // 5 sipariş oluştur
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]]
        ])->assertStatus(201);
    }

    // 6. sipariş başarısız olmalı
    $response = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]]
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Aynı gün içinde en fazla 5 sipariş oluşturabilirsiniz.',
        ]);
});

test('yetersiz stokta sipariş oluşturulamaz', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 2]);

    $response = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 5]]
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('success', false);
});

test('total_amount order items üzerinden hesaplanmalı', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create([
        'price' => 10000, // 100 TL (kuruş)
        'stock_quantity' => 10
    ]);

    $response = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 3] // 3 * 100 = 300 TL
        ]
    ]);

    $response->assertStatus(201);

    // Veritabanında toplam tutar kontrolü
    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'total_amount' => 30000, // 300 TL = 30000 kuruş
    ]);
});

// ============================================
// Status Geçiş Kuralları
// ============================================

test('pending sipariş completed yapılabilir', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    // Sipariş oluştur
    $orderResponse = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]]
    ]);

    $orderId = $orderResponse->json('data.id');

    // Completed yap
    $response = $this->patchJson("/api/orders/{$orderId}/status", [
        'status' => 'completed'
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status.value', 'completed');
});

test('pending sipariş cancelled yapılabilir', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $orderResponse = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]]
    ]);

    $orderId = $orderResponse->json('data.id');

    $response = $this->patchJson("/api/orders/{$orderId}/status", [
        'status' => 'cancelled'
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status.value', 'cancelled');
});

test('completed sipariş değiştirilemez', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $orderResponse = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]]
    ]);

    $orderId = $orderResponse->json('data.id');

    // Önce completed yap
    $this->patchJson("/api/orders/{$orderId}/status", ['status' => 'completed']);

    // Sonra cancelled yapmaya çalış
    $response = $this->patchJson("/api/orders/{$orderId}/status", [
        'status' => 'cancelled'
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
        ]);
});

test('cancelled sipariş güncellenemez', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $orderResponse = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]]
    ]);

    $orderId = $orderResponse->json('data.id');

    // Önce cancelled yap
    $this->patchJson("/api/orders/{$orderId}/status", ['status' => 'cancelled']);

    // Sonra pending yapmaya çalış
    $response = $this->patchJson("/api/orders/{$orderId}/status", [
        'status' => 'pending'
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'İptal edilmiş siparişler güncellenemez.',
        ]);
});
