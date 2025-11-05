<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // quien lo registró
            $table->enum('type', ['sale', 'income', 'expense'])->default('sale');
            $table->decimal('amount', 10, 2);
            $table->string('concept', 255)->nullable(); // Ej: “Dinero añadido a caja por falta de vuelto” o “Pasajes del empleado”
            $table->dateTime('movement_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
