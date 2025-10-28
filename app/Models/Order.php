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
        'payment_method_id',
        'payment_submethod_id',
        'payment_returned',
        'notes',
        'payment_amount',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'delivery_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'payment_returned' => 'decimal:2',
        'payment_amount' => 'decimal:2',
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

    // Relaciones con métodos de pago
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function paymentSubmethod()
    {
        return $this->belongsTo(PaymentSubmethod::class, 'payment_submethod_id');
    }
}