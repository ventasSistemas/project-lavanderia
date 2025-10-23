<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'employee_id',
        'branch_id',
        'total_amount',
        'discount',
        'tax',
        'final_total',
        'order_status_id',
        'receipt_date',
        'delivery_date',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'delivery_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}