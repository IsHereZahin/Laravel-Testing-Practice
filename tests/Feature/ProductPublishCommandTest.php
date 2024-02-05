<?php

namespace Tests\Feature;

use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductPublishCommandTest extends TestCase
{

    use RefreshDatabase;

    public function test_artisan_publish_product_successful(): void
    {
        // $product = Products::factory()->create();
        // $this->artisan('app:product-publish-command '. $product->id)->assertSuccessful();

        // $this->artisan('app:product-publish-command 1')->assertFailed();
        // $this->artisan('app:product-publish-command 1')->assertExitCode(-1);
        $this->artisan('app:product-publish-command 1')
            ->assertExitCode(-1)
            ->expectsOutput('Product not found');
    }
}
