<?php

namespace Tests\Feature;

use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_products_indexpage_contains_empty_table(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);

        $response->assertSee(__('No products found'));
    }

    public function test_products_indexpage_contains_non_empty_table(): void
    {
        // Arrange
        $product = Products::create([
            'name'  => 'Product 1',
            'price' => '10',
        ]);

        // Act
        $response = $this->get('/products');

        // Dump variable for debugging
        // dump($product); //Check Database in Terminal

        // Assert
        $response->assertStatus(200);
        $response->assertDontSee(__('No products found'));
        $response->assertSee('Product 1');
        $response->assertSee('name');
        $response->assertViewHas('products', function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record (): void
    {
        $products = Products::factory(11)->create();

        // dd($products);
        $lastProduct = $products->last();

        // for ($i = 1; $i <= 11; $i++) {
        //     $product = Products::create([
        //         'name'  => 'Product' . $i,
        //         'price' => rand(100,999),
        //     ]);
        // }

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($collection) use ($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }
}
