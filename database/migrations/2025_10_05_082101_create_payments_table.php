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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_type')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->enum('payment_type', ['cash', 'transfer'])->default('transfer');
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();
            
            $table->index(['no', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('payments');
    }
};
