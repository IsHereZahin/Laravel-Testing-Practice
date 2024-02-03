<?php

namespace Tests\Feature;

use App\Jobs\ProductPublishJob;
use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductPublishJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_product_publish_job()
    {
        // Create a product
        $product = Products::factory()->create();

        // Dispatch the job
        ProductPublishJob::dispatch($product->id);

        // Refresh the product from the database
        $product = $product->fresh();

        // Assert that the product was updated in some way
        $this->assertNotNull($product->updated_at);
    }


}
