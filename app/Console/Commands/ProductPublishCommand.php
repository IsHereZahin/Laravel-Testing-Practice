<?php

namespace App\Console\Commands;

use App\Models\Products;
use Illuminate\Console\Command;

class ProductPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:product-publish-command {id}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $product = Products::find($this->argument('id'));

        if (!$product) {
            $this->error('Product not found');

            return -1;
        }

        if ($product->published_at) {
            $this->error('Product already published');

            return -1;
        }

        $product->update(['published_at' => now()]);
        $this->info('Product published Successfully');
    }
}
