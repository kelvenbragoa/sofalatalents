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
        Schema::create('judge_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participation_id');
            $table->unsignedBigInteger('judge_id');
            $table->unsignedBigInteger('episode_id');
            $table->decimal('technical_score', 4, 2)->nullable();
            $table->decimal('interpretation_score', 4, 2)->nullable();
            $table->decimal('charisma_score', 4, 2)->nullable();
            $table->decimal('final_score', 4, 2);
            $table->text('comment')->nullable();
            $table->json('detailed_criteria')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judge_votes');
    }
};
