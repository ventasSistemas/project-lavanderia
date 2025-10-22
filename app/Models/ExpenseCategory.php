<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relación inversa: una categoría puede tener muchos gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}