<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id', 
        'full_name',
        'phone',
        'address',
        'document_number',
        'registration_date',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class); 
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}