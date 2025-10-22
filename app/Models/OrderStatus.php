<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_status';

    protected $fillable = [
        'name',
        'description',
        'color_code',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}