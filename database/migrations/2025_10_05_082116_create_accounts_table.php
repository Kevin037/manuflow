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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100);
            $table->string('name', 150);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->timestamps();
        });
        
        // Create index with prefix lengths for string columns
        DB::statement('CREATE INDEX accounts_code_name_index ON accounts (code(50), name(100))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
