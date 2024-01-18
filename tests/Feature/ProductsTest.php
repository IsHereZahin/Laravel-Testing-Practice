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

    public function test_admin_can_store_products_in_store()
    {
        // Creating an admin user
        $admin = User::factory()->create(['is_admin' => true]);

        // Acting as the admin user
        $this->actingAs($admin);

        // Creating a sample product data
        $productData = [
            'name' => 'Sample Product',
            'price' => 99.99,
        ];

        // Making a POST request to the store route
        $response = $this->post(route('product.store'), $productData);

        // Asserting that the product was created successfully
        $response->assertStatus(302); // Assuming redirect is used after successful creation
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_non_admin_cannot_store_product()
    {
        // Creating a non-admin user
        $user = User::factory()->create(['is_admin' => false]);

        // Act
        $this->actingAs($user);

        // Creating a sample product data
        $productData = [
            'name' => 'Sample Product',
            'price' => 99.99,
        ];

        // Making a POST request to the store route
        $response = $this->post(route('product.store'), $productData);

        // Asserting
        $response->assertStatus(403); // Assuming a forbidden status code is returned
        $this->assertDatabaseMissing('products', $productData);
    }

    public function test_admin_can_access_product_edit_page()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        $response->assertStatus(200);
    }

    public function test_non_admin_can_not_access_product_edit_page()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        $response->assertStatus(403);
    }

    public function test_products_edit_contains_correct_values()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->price);
        $response->assertViewHas('product', $product);
    }

    public function test_admin_can_update_product()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        $response->assertStatus(200);
        $productData = [
            'name' => 'Updated Product',
            'price' => 99.99,
        ];

        $response = $this->put(route('product.update', $product->id), $productData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_non_admin_can_not_update_product()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        $response->assertStatus(403);
        $productData = [
            'name' => 'Updated Product',
            'price' => 99.99,
        ];

        $response = $this->put(route('product.update', $product->id), $productData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', $productData);
    }

    public function test_product_update_validation_error_redirect_back_to_from()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->put(route('product.update', $product->id), [
            'name' => '',
            'price' => '',
        ]);

        $response->assertStatus(302);
        // $response->assertSessionHasErrors(['name']); // for single error
        $response->assertInvalid(['name', 'price']); // for multiple errors
    }

    public function test_admin_can_delete_product_successful()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->delete(route('product.destroy', $product->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', $product->toArray());
    }

    public function test_non_admin_can_not_delete_product()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->delete(route('product.destroy', $product->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', $product->toArray());
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }
}
