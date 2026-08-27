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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('episode_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('episode_type')->default('elimination');
            $table->timestamp('air_date');
            $table->unsignedBigInteger('eliminations')->default(0);
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');
            $table->boolean('voting_open')->default(false);
            $table->timestamp('voting_start')->nullable();
            $table->timestamp('voting_end')->nullable();
            $table->json('special_settings')->nullable();
            $table->decimal('jury_weight', 5, 2)->default(50.00);
            $table->decimal('public_weight', 5, 2)->default(50.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
