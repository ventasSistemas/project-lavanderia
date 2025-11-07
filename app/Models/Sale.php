<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'sale_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'amount_received',
        'change_given',
        'payment_method_id',
        'payment_submethod_id',
        'branch_id',
        'employee_id',
    ];

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function submethod()
    {
        return $this->belongsTo(PaymentSubmethod::class, 'payment_submethod_id');
    }
}
