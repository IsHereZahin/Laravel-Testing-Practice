<?php

namespace Tests\Feature;

use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductPriceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_product_price_set_successfully(): void
    {
        $product = new Products([
            'name'  => 'Product',
            'price' => 1.23,
        ]);

        $this->assertEquals(123, $product->price);
    }
}
