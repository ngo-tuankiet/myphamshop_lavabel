<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'productimage';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'url',
    ];

    public function getUrlAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function getRawUrlAttribute()
    {
        return $this->attributes['url'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
