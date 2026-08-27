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
        Schema::table('public_votes', function (Blueprint $table) {
            $table->string('payment_reference', 100)->nullable()->after('extra_data')->comment('Referência do pagamento da carteira móvel');
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_reference')->comment('Valor pago para o voto');
            $table->string('payment_phone', 20)->nullable()->after('payment_amount')->comment('Número de telefone usado no pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_amount', 'payment_phone']);
        });
    }
};
