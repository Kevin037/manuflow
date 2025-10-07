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
        // Make 'no' column nullable in formulas table
        Schema::table('formulas', function (Blueprint $table) {
            $table->string('no', 100)->nullable()->change();
        });

        // Make 'no' column nullable in productions table
        Schema::table('productions', function (Blueprint $table) {
            $table->string('no', 100)->nullable()->change();
        });

        // Make 'no' column nullable in purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('no', 100)->nullable()->change();
        });

        // Make 'no' column nullable in orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('no', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'no' column to not nullable in formulas table
        Schema::table('formulas', function (Blueprint $table) {
            $table->string('no', 100)->nullable(false)->change();
        });

        // Revert 'no' column to not nullable in productions table
        Schema::table('productions', function (Blueprint $table) {
            $table->string('no', 100)->nullable(false)->change();
        });

        // Revert 'no' column to not nullable in purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('no', 100)->nullable(false)->change();
        });

        // Revert 'no' column to not nullable in orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('no', 100)->nullable(false)->change();
        });
    }
};
