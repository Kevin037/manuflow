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
        Schema::table('materials', function (Blueprint $table) {
            // Add unit field
            $table->string('unit', 50)->after('price');
            
            // Drop the existing foreign key constraint
            $table->dropForeign(['supplier_id']);
            
            // Modify supplier_id to be nullable
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
            
            // Add foreign key constraint with SET NULL on delete
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Drop the unit field
            $table->dropColumn('unit');
            
            // Drop foreign key constraint
            $table->dropForeign(['supplier_id']);
            
            // Revert supplier_id to non-nullable
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
            
            // Re-add foreign key constraint with cascade delete
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }
};
