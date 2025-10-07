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
        // Fix payments table - bank_account_id should be nullable with SET NULL
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->unsignedBigInteger('bank_account_id')->nullable()->change();
            $table->foreign('bank_account_id')->references('id')->on('accounts')->onDelete('set null');
        });

        // Fix accounts table - parent_id should be nullable with SET NULL  
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->unsignedBigInteger('parent_id')->nullable()->change();
            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('set null');
        });

        // Note: We'll keep CASCADE for critical relationships like:
        // - order_details -> orders (if order is deleted, details should be deleted)
        // - purchase_order_details -> purchase_orders (if PO is deleted, details should be deleted)
        // - formula_details -> formulas (if formula is deleted, details should be deleted)
        // - invoice -> order (if order is deleted, invoice should be deleted)
        // - payment -> invoice (if invoice is deleted, payment should be deleted)
        // - journal_entries -> account (accounting integrity)
        
        // But we should consider making some relationships use SET NULL for data preservation:
        
        // Orders -> customers: when customer deleted, preserve order data
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // Purchase orders -> suppliers: when supplier deleted, preserve PO data
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });

        // Productions -> products: when product deleted, preserve production data
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->unsignedBigInteger('bank_account_id')->nullable()->change();
            $table->foreign('bank_account_id')->references('id')->on('accounts')->onDelete('set null');
        });

        // Revert accounts table
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->unsignedBigInteger('parent_id')->nullable()->change();
            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('set null');
        });

        // Revert orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->unsignedBigInteger('customer_id')->change();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        // Revert purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->unsignedBigInteger('supplier_id')->change();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });

        // Revert productions table
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
