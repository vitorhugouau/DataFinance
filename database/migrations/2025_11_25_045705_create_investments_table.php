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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->string('type'); // stock, crypto, fixed_income, etc
            $table->string('symbol'); // BTC, ETH, USD, EUR, etc
            $table->string('name'); // Bitcoin, Ethereum, etc
            $table->decimal('amount', 15, 8); // Quantidade
            $table->decimal('purchase_price', 15, 2); // Preço de compra
            $table->date('purchase_date');
            $table->decimal('current_price', 15, 2)->nullable(); // Preço atual
            $table->decimal('interest_rate', 5, 2)->nullable(); // Taxa de juros (para renda fixa)
            $table->string('interest_type')->nullable(); // monthly, yearly, etc
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
