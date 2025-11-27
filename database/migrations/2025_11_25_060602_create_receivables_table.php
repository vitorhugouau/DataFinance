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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('debtor_name'); // Nome de quem deve
            $table->text('description')->nullable(); // Descrição do que é devido
            $table->decimal('amount', 12, 2); // Valor devido
            $table->date('due_date'); // Data de vencimento
            $table->date('paid_date')->nullable(); // Data de pagamento (se já foi pago)
            $table->boolean('paid')->default(false); // Se foi pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
