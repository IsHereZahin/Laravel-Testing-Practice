<?php

namespace Tests\Feature\Api;

use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_api_returns_products_list(): void
    {
        $product = Products::factory()->create();
        $response = $this->getJson('/api/products');

        $response->assertJson([$product->toArray()]);
    }

    public function test_api_product_store_successful(): void
    {
        $product = [
            'name' => 'test',
            'price' => 100,
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(200);
        $response->assertJson($product);
    }

    public function test_api_product_invalid_store_returns_error(): void
    {
        $product = [
            'name' => '',
            'price' => 100,
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(422);
    }
}
