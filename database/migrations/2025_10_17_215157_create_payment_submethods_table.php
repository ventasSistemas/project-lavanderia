<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_submethods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ej: Yape, Plin, BCP, Interbank
            $table->string('recipient_name')->nullable(); // Nombre del destinatario
            $table->string('account_number')->nullable(); // Número de cuenta o número de billetera
            $table->string('identifier')->nullable(); // Ej: número de operación, alias, etc.
            $table->string('additional_info')->nullable(); // Otros datos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_submethods');
    }
};