<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCombo extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'combo_service');
    }
}