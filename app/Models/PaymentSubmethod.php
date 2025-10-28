<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSubmethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'name',
        'recipient_name',
        'account_number',
        'identifier',
        'additional_info',
    ];

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}