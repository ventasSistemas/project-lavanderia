<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_id',
        'opening_date',
        'closing_date',
        'status',
        'schedule',
        'is_open',
        'code_letter'
    ];

    protected $casts = [
        'opening_date' => 'date',
        'closing_date' => 'date',
        'schedule' => 'array', 
        'is_open' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /*
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }*/
}