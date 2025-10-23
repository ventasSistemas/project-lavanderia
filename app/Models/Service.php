<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'base_price',
        'unit_type',
        'estimated_time',
        'status',
        'image'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /*
    public function items()
    {
        return $this->hasMany(ServiceItem::class);
    }

    public function combos()
    {
        return $this->belongsToMany(ServiceCombo::class, 'combo_service');
    }*/
}