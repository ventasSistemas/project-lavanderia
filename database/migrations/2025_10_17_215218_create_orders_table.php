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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('payment_submethod_id')->nullable()->constrained('payment_submethods')->nullOnDelete();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->after('employee_id');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('final_total', 10, 2)->default(0);
            $table->foreignId('order_status_id')->constrained('order_status')->onDelete('restrict');
            $table->decimal('payment_returned', 10, 2)->default(0);
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->dateTime('receipt_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
