<?php

namespace App\Services;

use App\Models\Products;
use Brick\Math\Exception\NumberFormatException;

class ProductService {
    public function create(string $name, int $price): Products
    {
        if ($price < 10) {
            throw new NumberFormatException('Price must be greater than 10');
        }

        elseif ($price > 1000) {
            throw new NumberFormatException('Price must be less than 1000');
        }

        return Products::create([
            'name'  => $name,
            'price' => $price,
        ]);
    }
}
