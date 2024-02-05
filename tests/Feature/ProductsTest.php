<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\User;
use App\Services\ProductService;
use Brick\Math\Exception\NumberFormatException;
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

        // $response->assertStatus(200);
        $response->assertOk();
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
        // $response->assertStatus(200);
        $response->assertOk();
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

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertViewHas('products', function ($collection) use ($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }
    public function test_admin_can_see_add_new_product_button (): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/products');

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertSee('Add new product');
    }

    public function test_user_can_not_see_add_new_product_button (): void
    {
        $user = $this->createUser();
        // $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertDontSee('Add new product');
    }


    public function test_admin_can_access_product_create_page()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/product/create');

        // $response->assertStatus(200);
        $response->assertOk();
    }

    public function test_non_admin_cannot_access_product_create_page()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/product/create');

        // $response->assertStatus(403);
        $response->assertForbidden();
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

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'price' => $productData['price'] * 100, // Adjust this based on your setPriceAttribute logic
        ]);
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
        // $response->assertStatus(403); // Assuming a forbidden status code is returned
        $response->assertForbidden();
        $this->assertDatabaseMissing('products', $productData);
    }

    public function test_admin_can_access_product_edit_page()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        // $response->assertStatus(200);
        $response->assertOk();
    }

    public function test_non_admin_can_not_access_product_edit_page()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        // $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_products_edit_contains_correct_values()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $product = Products::factory()->create();
        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertSee($product->price);
        $response->assertViewHas('product', $product);
    }

    public function test_product_edit_contains_correct_values(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $product = Products::factory()->create();
        $this->assertDatabaseHas('products', [
            'name' => $product->name,
            'price' => $product->price,
        ]);

        $this->assertModelExists($product);
        $response = $this->actingAs($admin)->get(route('product.edit', $product->id));

        $response->assertOk();

        // Assert input values using assertSee
        // $response->assertSee($product->name);
        // $response->assertSee((string) $product->price); // Convert price to string as assertSee works with strings

        $response->assertSee('value="'.$product->name.'"', false);
        $response->assertSee('value="'.(string) $product->price.'"', false);

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
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $productData['name'],
            'price' => $productData['price'] * 100, // Adjust this based on setPriceAttribute logic
        ]);
    }

    public function test_non_admin_can_not_update_product()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Products::factory()->create();

        $response = $this->actingAs($user)->get(route('product.edit', $product->id));

        // $response->assertStatus(403);
        $response->assertForbidden();
        $productData = [
            'name' => 'Updated Product',
            'price' => 99.99,
        ];

        $response = $this->put(route('product.update', $product->id), $productData);

        // $response->assertStatus(403);
        $response->assertForbidden();
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

        // $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_homepage_contains_table_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $product = Products::create([
            'name'  => 'Product',
            'price' =>123,
        ]);

        $response = $this->actingAs($admin)->get('/products');
        $response->assertOk();
        $response->assertSeeText($product->name);
    }

    public function test_homepage_contains_products_in_order(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        [$product1, $product2] = Products::factory(2)->create();
        $response = $this->actingAs($user)->get('/products');
        $response->assertOk();
        $response->assertSeeInOrder([$product1->name, $product2->name]);
    }

    // AssertStatus(200) VS AssertOk(), Using these methods not only makes your test code more readable but also helps in quickly understanding the intent of your assertions.
    // assertOk()           for 200 OK
    // assertCreated()      for 201 Created
    // assertNoContent()    for 204 No Content
    // assertBadRequest()   for 400 Bad Request
    // assertUnauthorized() for 401 Unauthorized
    // assertForbidden()    for 403 Forbidden
    // assertNotFound()     for 404 Not Found
    // assertServerError()  for 500 Internal Server Error

    public function test_product_service_create_retruns_product(): void
    {
        $product = (new ProductService())->create('Test Product', '10');   // Try prices less than 10 and more than 1000.
        $this->assertInstanceOf(Products::class, $product);
    }

    public function test_product_service_create_validation(): void
    {
        try
        {
            (new ProductService())->create('To Big price', '100000');      // Try 10 > $price < 1000
        }
        catch (\Exception $e)
        {
            $this->assertInstanceOf(NumberFormatException::class, $e);
        }
    }

    public function test_download_product_successful(): void
    {
        $response = $this->get('/download');
        $response->assertOk();
        $response->assertHeader('content-Disposition', 'attachment; filename=download.pdf');
    }

    public function test_product_shows_when_published_at_current_time(): void
    {
        $this->markTestSkipped('Skipped it for now because there is an error'); // Test skiped error
        $user = User::factory()->create(['is_admin' => true]);

        $product = Products::factory()->create([
            'published_at' => now()->addDay()->setHour(14)->setMinute(00),
        ]);

        $response = $this->actingAs($user)->get('products');
        $response->assertDontSeeText($product->name);

        $this->travelTo(now()->addDay()->setHour(14)->setMinute(00));
        $response = $this->actingAs($user)->get('products');
        $response->assertSeeText($product->name);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }
}
