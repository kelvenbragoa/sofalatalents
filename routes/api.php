<?php

use App\Http\Controllers\Api\Web\AuthWebController;
use App\Http\Controllers\Api\Web\UserController;
use App\Http\Controllers\Api\Web\ParticipantController as AdminParticipantController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\EliminationController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\Sanctum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login',[AuthWebController::class,'login']);

Route::middleware([Sanctum::class])->group(function () {
    
    Route::post('logout',[AuthWebController::class,'logout']);

    Route::resource('users', UserController::class);

});

// Admin Routes - Protected (authentication required)
Route::middleware([Sanctum::class])->prefix('web')->group(function () {
    // Participants CRUD (admin only)
    Route::resource('participants', AdminParticipantController::class);
    Route::post('participants/{participant}/toggle-status', [AdminParticipantController::class, 'toggleStatus']);
    Route::get('participants/{participant}/stats', [AdminParticipantController::class, 'stats']);
    
    // Episodes CRUD (admin only)
    Route::resource('episodes', \App\Http\Controllers\Api\Web\EpisodeController::class);
    Route::post('episodes/{episode}/toggle-voting', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'toggleVoting']);
    Route::post('episodes/{episode}/change-status', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'changeStatus']);
    Route::get('episodes/{episode}/stats', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'stats']);
    Route::get('episodes/{episode}/results', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'results']);
    Route::get('episodes/participants/available', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'availableParticipants']);
    Route::post('episodes/{episode}/eliminate-participants', [\App\Http\Controllers\Api\Web\EpisodeController::class, 'eliminateParticipants']);
    
    // Elimination management (admin only)
    Route::post('episodes/{episode}/eliminate', [EliminationController::class, 'eliminate']);
    Route::post('episodes/{episode}/revert', [EliminationController::class, 'revert']);
    Route::get('episodes/{episode}/simulate', [EliminationController::class, 'simulate']);

    Route::post('judgevotes', [VoteController::class, 'storejudge']);
});

// VotaAqui Routes - Public routes (no authentication required)
Route::prefix('votaaqui')->group(function () {
    // Payment routes
    Route::post('payments/process', [PaymentController::class, 'processPayment']);
    
    // Participants routes
    Route::get('participants', [ParticipantController::class, 'index']);
    Route::get('participants/{participant}', [ParticipantController::class, 'show']);
    
    // Voting routes
    
    Route::post('votes', [VoteController::class, 'store']);
    Route::get('votes/statistics', [VoteController::class, 'statistics']);
    Route::get('participants/{participant}/votes', [VoteController::class, 'participantVotes']);
    
    // Episodes routes (public)
    Route::get('episodes', [EpisodeController::class, 'index']);
    Route::get('episodes/current', [EpisodeController::class, 'current']);
    Route::get('episodes/{episode}', [EpisodeController::class, 'show']);
    Route::get('episodes/{episode}/results', [EpisodeController::class, 'results']);
    Route::get('eliminations/history', [EpisodeController::class, 'eliminationHistory']);
    Route::get('statistics', [EpisodeController::class, 'statistics']);
});

// Legacy routes for compatibility
Route::get('participants', [ParticipantController::class, 'index']);
Route::post('votes', [VoteController::class, 'store']);