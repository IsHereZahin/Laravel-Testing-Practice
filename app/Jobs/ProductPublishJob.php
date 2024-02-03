<?php

namespace App\Jobs;

use App\Models\Products;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductPublishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $productId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = Products::find($this->productId);

        if ($product && !$product->published_at) {
            $product->update(['published_at' => now()]);
        }
    }
}
