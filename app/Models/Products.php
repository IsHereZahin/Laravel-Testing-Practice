<?php

namespace App\Models;

use App\Services\CurrencyService;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'price',
    ];

    public function getPriceBdtAttribute()
    {
        return (new CurrencyService())->convert($this->price, 'usd', 'bdt');
    }


    public function getPriceRubAttribute() {
        return (new CurrencyService())->convert($this->price, 'usd', 'rub');
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
