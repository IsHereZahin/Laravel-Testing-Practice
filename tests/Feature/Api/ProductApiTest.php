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
}
