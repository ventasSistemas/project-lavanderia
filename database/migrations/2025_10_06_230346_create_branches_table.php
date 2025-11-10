<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->text('address');
            $table->string('phone', 9)->nullable();
            $table->string('email', 120)->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->date('opening_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('schedule');
            $table->boolean('is_open')->default(false);
             $table->char('code_letter', 1)->unique('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
