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
        Schema::table('participations', function (Blueprint $table) {
            // Rename contestant_id to participant_id
            // $table->renameColumn('contestant_id', 'participant_id');
            
            // Add missing columns
            // $table->enum('status', ['active', 'eliminated', 'saved'])->default('active')->after('episode_id');
            // $table->integer('position')->nullable()->after('status');
            // $table->integer('elimination_order')->nullable()->after('position');
            // $table->timestamp('eliminated_at')->nullable()->after('elimination_order');
            
            // Add foreign key constraints
            // $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            // $table->foreign('episode_id')->references('id')->on('episodes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['participant_id']);
            $table->dropForeign(['episode_id']);
            
            // Drop added columns
            $table->dropColumn(['status', 'position', 'elimination_order', 'eliminated_at']);
            
            // Rename back to contestant_id
            $table->renameColumn('participant_id', 'contestant_id');
        });
    }
};
