<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplementaryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'complementary_product_category_id',
        'name',
        'price',
        'image',
        'state',
    ];

    public function category()
    {
        return $this->belongsTo(ComplementaryProductCategory::class, 'complementary_product_category_id');
    }
}