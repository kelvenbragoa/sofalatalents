<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $fillable = [
        'name',
        'stage_name',
        'age',
        'gender',
        'city',
        'province',
        'phone',
        'email',
        'photo_url',
        'biography',
        'skills',
        'social_links',
        'voting_code',
        'active',
        'eliminated_episode_id',
        'final_position',
        'overall_average',
    ];

    protected $casts = [
        'active' => 'boolean',
        'age' => 'integer',
        'final_position' => 'integer',
        'overall_average' => 'decimal:2',
    ];

    /**
     * Get the episode where this participant was eliminated
     */
    public function eliminatedEpisode(): BelongsTo
    {
        return $this->belongsTo(Episode::class, 'eliminated_episode_id');
    }

    /**
     * Get all public votes for this participant through participations
     */
    public function publicVotes(): HasManyThrough
    {
        return $this->hasManyThrough(
            PublicVote::class,
            Participation::class,
            'contestant_id', // Foreign key on participations table
            'participation_id', // Foreign key on public_votes table
            'id', // Local key on participants table
            'id' // Local key on participations table
        );
    }

    /**
     * Get all judge votes for this participant through participations
     */
    public function judgeVotes(): HasManyThrough
    {
        return $this->hasManyThrough(
            JudgeVote::class,
            Participation::class,
            'contestant_id', // Foreign key on participations table
            'participation_id', // Foreign key on judge_votes table
            'id', // Local key on participants table
            'id' // Local key on participations table
        );
    }

    /**
     * Get all participations (episodes) for this participant
     */
    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class, 'contestant_id');
    }

    /**
     * Check if the participant is still active in the competition
     */
    public function isActive(): bool
    {
        return $this->active && is_null($this->eliminated_episode_id);
    }

    /**
     * Get the total number of public votes
     */
    public function getTotalPublicVotesAttribute(): int
    {
        return $this->publicVotes()->count();
    }

    /**
     * Get the participant's display name (stage name or real name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->stage_name ?: $this->name;
    }

    /**
     * Scope to get only active participants
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)->whereNull('eliminated_episode_id');
    }

    /**
     * Scope to get eliminated participants
     */
    public function scopeEliminated($query)
    {
        return $query->whereNotNull('eliminated_episode_id');
    }
}
