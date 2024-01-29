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

        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
        ]);
    }

    public function test_api_returns_product_list(): void
    {
        $product1 = Products::factory()->create();
        $product2 = Products::factory()->create();
        $response = $this->getJson('/api/products');

        $response->assertJsonFragment([
            'name'  => $product1->name,
            'price' => $product1->price,
        ]);

        $response->assertJsonCount(2, 'data');
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

    public function test_api_products_show_successful(): void
    {
        $productData = [
            'name' => 'test',
            'price' => 100,
        ];
        $product = Products::create($productData);

        $response = $this->getJson("/api/products/{$product->id}");
        $response->assertOk();
        $response->assertJsonPath('data.name', $productData['name']);
        $response->assertJsonMissingPath('data.created_at');

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'price',
            ]
        ]);
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

    public function test_api_product_invalid_store_return_error(): void
    {
        $product = [
            'name' => 'test',
            'price' => '', // This should be set to a non-empty value to trigger the validation error
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('price');
        $response->assertInvalid('price');
        // /OR
        // $response->assertJson([
        //     'errors' => [
        //         'price' => [
        //             'The price field is required.',
        //         ],
        //     ],
        // ]);
    }

    public function test_api_product_update_successful(): void
    {
        // Create a product
        $productData = [
            'name' => 'test',
            'price' => 100,
        ];
        $product = Products::create($productData);

        // Update the product
        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Product Update',
            'price' => 200,
        ]);

        // Assert that the update was successful
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Product Update',
            'price' => 200,
        ]);

        $response->assertJsonMissing($productData);
    }

    public function test_api_product_invalid_update_returns_error(): void
    {
        // Create a product
        $productData = [
            'name' => 'test',
            'price' => 100,
        ];
        $product = Products::create($productData);

        // Update the product
        $response = $this->putJson('/api/products/'. $product->id, [
            'name' => '',
            'price' => 200,
        ]);

        // Assert that the update was successful
        $response->assertStatus(422);
    }



}
