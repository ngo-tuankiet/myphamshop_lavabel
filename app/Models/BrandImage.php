<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandImage extends Model
{
    use HasFactory;

    protected $table = 'brandimage'; 
    public $timestamps = false; 

    protected $fillable = [
        'brand_id',
        'url',
    ];
    
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
}