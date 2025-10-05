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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no', 100)->nullable();
            $table->date('dt');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->timestamps();
        });
        
        // Create index with prefix length for string column
        DB::statement('CREATE INDEX invoices_no_dt_index ON invoices (no(50), dt)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
