<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('item_name', 100);
            $table->decimal('price', 10, 2);
            $table->text('includes')->nullable(); // Ejemplo: "Lavado + secado"
            $table->decimal('additional_price', 10, 2)->nullable(); // Ej: planchado adicional
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};