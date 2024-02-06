<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{

    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->admin = $this->createUser(isAdmin: true);
    }

    public function test_product_create_photo_upload_successful(): void
    {
        Storage::fake();
        $filename = 'image.jpg';

        $product = [
            'name' => 'Product name',
            'price' => 100,
            'image' => UploadedFile::fake()->image($filename),
        ];

        $response = $this->actingAs($this->admin)->post(route('product.store'), $product);
        $response->assertRedirect(route('products.index'));

        $lastProduct = Products::latest()->first();
        $this->assertNotNull($lastProduct->image);

        // Ensure the full path to the image matches
        $imageFullPath = public_path($lastProduct->image);
        $this->assertFileExists($imageFullPath);
    }

    private function createUser(bool $isAdmin = false): User
    {
        return User::factory()->create([
            'is_admin' => $isAdmin,
        ]);
    }
}
