<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductNotification extends Model
{
    use HasFactory;

    protected $fillable = ['product_transfer_id', 'user_id', 'message', 'is_read'];

    public function transfer()
    {
        return $this->belongsTo(ProductTransfer::class, 'product_transfer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}