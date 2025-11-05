<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'opening_amount',
        'closing_amount',
        'total_sales',
        'total_income',
        'total_expense',
        'opened_at',
        'closed_at',
        'status',
    ];

    protected $dates = ['opened_at', 'closed_at'];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function notifications()
    {
        return $this->hasMany(CashNotification::class);
    }

}