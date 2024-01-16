<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\User;
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
        // $user = User::factory()->create(); // If you don't use method you can use it
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);

        $response->assertSee(__('No products found'));
    }

    public function test_products_indexpage_contains_non_empty_table(): void
    {
        $user = $this->createUser();
        // Arrange
        $product = Products::create([
            'name'  => 'Product 1',
            'price' => '10',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/products');

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
        $user = $this->createUser();
        $products = Products::factory(11)->create();

        // dd($products);
        $lastProduct = $products->last();

        // for ($i = 1; $i <= 11; $i++) {
        //     $product = Products::create([
        //         'name'  => 'Product' . $i,
        //         'price' => rand(100,999),
        //     ]);
        // }

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($collection) use ($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }
    public function test_admin_can_see_add_new_product_button (): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Add new product');
    }

    public function test_user_can_not_see_add_new_product_button (): void
    {
        $user = $this->createUser();
        // $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('Add new product');
    }


    public function test_admin_can_access_product_create_page()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $response = $this->actingAs($admin)->get('/product/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_product_create_page()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/product/create');

        $response->assertStatus(403);
    }


    public function createUser(): mixed
    {
        return User::factory()->create();
    }
}
