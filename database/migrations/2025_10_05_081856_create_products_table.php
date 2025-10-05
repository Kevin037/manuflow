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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('sku', 100);
            $table->string('photo');
            $table->double('price');
            $table->double('qty');
            $table->foreignId('formula_id')->constrained('formulas')->onDelete('cascade');
            $table->timestamps();
        });
        
        // Use raw SQL to create index with prefix lengths
        DB::statement('CREATE INDEX products_name_sku_index ON products (name(100), sku(50))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
