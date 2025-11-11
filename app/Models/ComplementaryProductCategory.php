<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplementaryProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'branch_id', 
    ];

    public function products()
    {
        return $this->hasMany(ComplementaryProduct::class, 'complementary_product_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Saber si la categoría es global (de admin)
    public function isGlobal()
    {
        return is_null($this->branch_id);
    }
}

