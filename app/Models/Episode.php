<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Blameable;

class Episode extends Model
{
    // use Blameable;

    protected $fillable = [
        'episode_number',
        'title',
        'description',
        'episode_type',
        'air_date',
        'eliminations',
        'status',
        'voting_open',
        'voting_start',
        'voting_end',
        'special_settings',
        'jury_weight',
        'public_weight',
        'notes'
    ];

    protected $casts = [
        'air_date' => 'datetime',
        'voting_start' => 'datetime',
        'voting_end' => 'datetime',
        'special_settings' => 'array',
        'voting_open' => 'boolean',
        'jury_weight' => 'decimal:2',
        'public_weight' => 'decimal:2'
    ];

    // Relationships
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'participations')
                    ->withPivot(['status', 'position', 'elimination_order', 'eliminated_at'])
                    ->withTimestamps();
    }

    public function eliminatedParticipants(): HasMany
    {
        return $this->hasMany(Participant::class, 'eliminated_episode_id');
    }

    public function judgeVotes(): HasMany
    {
        return $this->hasMany(JudgeVote::class, 'episode_id');
    }

    public function publicVotes(): HasManyThrough
    {
        // Public votes are related through participations
        return $this->hasManyThrough(
            PublicVote::class,
            Participation::class,
            'episode_id',      // Foreign key on participations table
            'participation_id', // Foreign key on public_votes table
            'id',              // Local key on episodes table
            'id'               // Local key on participations table
        );
    }

    // Scopes
    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeWithVotingOpen($query)
    {
        return $query->where('voting_open', true);
    }

    // Methods
    public function isVotingActive(): bool
    {
        if (!$this->voting_open) {
            return false;
        }

        $now = now();
        
        if ($this->voting_start && $now->lt($this->voting_start)) {
            return false;
        }

        if ($this->voting_end && $now->gt($this->voting_end)) {
            return false;
        }

        return true;
    }

    /**
     * Get the current active episode for voting
     * Reality show rule: Only one episode should be active at a time
     */
    public static function getCurrentActiveEpisode(): ?Episode
    {
        return static::where('voting_open', true)
            ->where('status', 'live')
            ->orderBy('episode_number', 'desc')
            ->first();
    }

    /**
     * Close all other episodes' voting when opening a new one
     */
    public function openVotingExclusively(): bool
    {
        // Close all other episodes
        static::where('id', '!=', $this->id)->update(['voting_open' => false]);
        
        // Open this episode and set to live
        return $this->update([
            'voting_open' => true,
            'status' => 'live'
        ]);
    }

    public function getVotingResults(): array
    {
        $results = [];
        $participants = $this->participants;

        foreach ($participants as $participant) {
            // Get participation for this episode and participant
            $participation = Participation::where('episode_id', $this->id)
                ->where('participant_id', $participant->id)
                ->first();

            if (!$participation) {
                continue;
            }

            // Count public votes for this participation
            $publicCount = PublicVote::where('participation_id', $participation->id)->count();

            // Get judge votes for this participation
            $judgeVotes = JudgeVote::where('participation_id', $participation->id)->get();
            $judgeCount = $judgeVotes->count();
            $judgeAvg = $judgeCount > 0 ? $judgeVotes->avg('final_score') : 0;
            
            $results[] = [
                'participant_id' => $participant->id,
                'participant_name' => $participant->name,
                'participant_photo' => $participant->photo_url,
                'public_votes' => $publicCount,
                'judge_votes' => $judgeCount,
                'judge_average' => (float) $judgeAvg,
                'total_score' => ($publicCount * ($this->public_weight ?? 50) / 100) + 
                               ($judgeAvg * ($this->jury_weight ?? 50) / 100)
            ];
        }

        return collect($results)->sortByDesc('total_score')->values()->toArray();
    }

    public function eliminateParticipants(array $participantIds): bool
    {
        try {
            Participant::whereIn('id', $participantIds)
                ->update([
                    'eliminated_episode_id' => $this->id,
                    'active' => false,
                    'elimination_date' => now()
                ]);

            $this->update(['eliminations' => count($participantIds)]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
