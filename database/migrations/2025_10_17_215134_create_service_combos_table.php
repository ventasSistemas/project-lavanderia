<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_combos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // Relación pivot (combo - servicios)
        Schema::create('combo_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_combo_id')->constrained('service_combos')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_service');
        Schema::dropIfExists('service_combos');
    }
};