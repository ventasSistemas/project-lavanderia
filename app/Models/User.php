<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'branch_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // RELACIONES
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'employee_id');
    }

    /*
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'generated_by');
    }*/

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }
}