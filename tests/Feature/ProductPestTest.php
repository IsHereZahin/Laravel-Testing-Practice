<?php

use App\Models\Products;

beforeEach(function() {
    $this->user = createuser();                     // The Pest.php file has functions to help create this
    $this->admin = createuser(isAdmin:true);
});

test('Home page contains empty table', function() {
    $this->actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertSee(__('No products found'));});

test('Products indexpage contains non empty table', function(){
         // Arrange
         $product = Products::create([
            'name'  => 'Product 1',
            'price' => '10',
        ]);

        // Act
        $this->actingAs($this->user)
            ->get('/products')

        // Assert
        ->assertStatus(200)
        ->assertDontSee(__('No products found'))
        ->assertSee('Product 1')
        ->assertSee('name')
        ->assertViewHas('products', function ($collection) use ($product) {
            return $collection->contains($product);
        });
});
