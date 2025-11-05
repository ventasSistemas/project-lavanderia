<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // quién abre la caja
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('opening_amount', 10, 2)->default(0); // fondo inicial
            $table->decimal('closing_amount', 10, 2)->nullable(); // saldo final
            $table->decimal('total_sales', 10, 2)->default(0); // total ventas
            $table->decimal('total_income', 10, 2)->default(0); // total ingresos manuales
            $table->decimal('total_expense', 10, 2)->default(0); // total egresos
            $table->dateTime('opened_at')->nullable(); // fecha apertura
            $table->dateTime('closed_at')->nullable(); // fecha cierre
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
