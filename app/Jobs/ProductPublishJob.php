<?php

namespace App\Jobs;

use App\Models\Products;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class ProductPublishJob implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    protected $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    public function handle()
    {
        $product = Products::find($this->productId);

        if ($product) {
            // Update the published_at attribute
            $product->published_at = now();
            $product->save();
        }
    }
}
