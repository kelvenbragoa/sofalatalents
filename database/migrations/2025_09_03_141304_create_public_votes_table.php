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
        Schema::create('public_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participation_id');
            $table->string('vote_method');
            $table->string('voter_identifier');
            $table->string('used_code', 10);
            $table->decimal('vote_value', 10, 2)->default(0);
            $table->string('operator')->nullable();
            $table->string('country', 2)->default('MZ');
            $table->boolean('validated')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('extra_data')->nullable();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_votes');
    }
};
