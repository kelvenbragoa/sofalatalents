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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stage_name')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['M', 'F', 'Other'])->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo_url')->nullable();
            $table->text('biography')->nullable();
            $table->text('skills')->nullable();
            $table->text('social_links')->nullable();
            $table->string('voting_code', 10)->unique();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('eliminated_episode_id')->nullable();
            $table->integer('final_position')->nullable();
            $table->decimal('overall_average', 4, 2)->nullable();
            $table->timestamps();
            
            // We'll add the foreign key constraint after episodes table is created
            // $table->foreignId('eliminated_episode_id')->nullable()->constrained('episodes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
