<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'amount')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->decimal('amount', 18, 2)->default(0)->after('payment_type');
            });
        }
    }

    public function down(): void
    {
        // Only drop the column if it exists (safe rollback)
        if (Schema::hasColumn('payments', 'amount')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
};
