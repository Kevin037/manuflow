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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->date('dt');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->double('qty');
            $table->longText('notes')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->timestamps();
            
            $table->index(['no', 'dt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
