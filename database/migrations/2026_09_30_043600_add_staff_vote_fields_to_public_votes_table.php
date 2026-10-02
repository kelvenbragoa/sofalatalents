<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->unsignedInteger('vote_quantity')->default(1)->after('vote_value');
            $table->decimal('unit_price', 10, 2)->default(30)->after('vote_quantity');
            $table->string('voter_name')->nullable()->after('unit_price');
            $table->string('receipt_number', 40)->nullable()->after('voter_name');
            $table->unsignedBigInteger('staff_user_id')->nullable()->after('receipt_number');

            $table->unique('receipt_number');
            $table->index('staff_user_id');
            $table->foreign('staff_user_id')->references('id')->on('users')->nullOnDelete();
        });

        if (!DB::table('roles')->where('name', 'Staff')->exists()) {
            DB::table('roles')->insert([
                'name' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->dropForeign(['staff_user_id']);
            $table->dropUnique(['receipt_number']);
            $table->dropIndex(['staff_user_id']);
            $table->dropColumn([
                'vote_quantity',
                'unit_price',
                'voter_name',
                'receipt_number',
                'staff_user_id',
            ]);
        });
    }
};
