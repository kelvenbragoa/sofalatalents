<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudgeVote extends Model
{
    protected $fillable = [
        'participation_id',
        'judge_id',
        'episode_id',
        'technical_score',
        'interpretation_score',
        'charisma_score',
        'final_score',
        'comment',
        'detailed_criteria',
        'evaluated_at',
    ];

    protected $casts = [
        'technical_score' => 'decimal:2',
        'interpretation_score' => 'decimal:2',
        'charisma_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'detailed_criteria' => 'array',
        'evaluated_at' => 'datetime',
    ];

    /**
     * Get the participation that received this vote
     */
    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    /**
     * Get the judge who gave this vote
     */
    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class);
    }

    /**
     * Get the episode for this vote
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
