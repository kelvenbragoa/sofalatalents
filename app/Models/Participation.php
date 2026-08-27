<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participation extends Model
{
    protected $fillable = [
        'participant_id',
        'episode_id',
        'status',
        'position',
        'elimination_order',
        'eliminated_at',
        'performance_data',
        'jury_score',
        'public_votes',
        'public_score',
        'final_score',
        'episode_position',
        'eliminated',
        'saved_by_public',
        'judge_comments',
    ];

    protected $casts = [
        'jury_score' => 'decimal:2',
        'public_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'eliminated' => 'boolean',
        'saved_by_public' => 'boolean',
        'public_votes' => 'integer',
        'episode_position' => 'integer',
        'position' => 'integer',
        'elimination_order' => 'integer',
        'eliminated_at' => 'datetime',
    ];

    /**
     * Get the participant for this participation
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * Get the episode for this participation
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    /**
     * Get all public votes for this participation
     */
    public function publicVotes(): HasMany
    {
        return $this->hasMany(PublicVote::class);
    }

    /**
     * Get all judge votes for this participation
     */
    public function judgeVotes(): HasMany
    {
        return $this->hasMany(JudgeVote::class);
    }
}
