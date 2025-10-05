<?php
// adjusted index lengths to avoid MySQL 1071 error

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no', 100);
            $table->date('dt');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->double('total');
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->timestamps();
        });
        
        // Create index with prefix length for string column
        DB::statement('CREATE INDEX purchase_orders_no_dt_index ON purchase_orders (no(50), dt)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
