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
        Schema::table('credit_card_expenses', function (Blueprint $table) {
            $table->decimal('total_value', 12, 2)->nullable()->after('value');
            $table->integer('installments')->nullable()->after('total_value');
            $table->integer('current_installment')->default(1)->after('installments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_card_expenses', function (Blueprint $table) {
            $table->dropColumn(['total_value', 'installments', 'current_installment']);
        });
    }
};
