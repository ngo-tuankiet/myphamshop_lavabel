<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'brand_id',
        'subcategory_id', 
        'barcode',
        'origin',
        'manufacture_country',
        'skin_type',
        'volume',
        'scent',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'subcategory_id', 'id');
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class, 'product_id', 'id');
    }
}