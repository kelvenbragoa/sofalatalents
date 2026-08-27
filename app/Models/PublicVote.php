<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicVote extends Model
{
    protected $fillable = [
        'participation_id',
        'vote_method',
        'voter_identifier',
        'used_code',
        'vote_value',
        'operator',
        'country',
        'validated',
        'ip_address',
        'user_agent',
        'extra_data',
        'voted_at',
        'payment_reference',
        'payment_amount',
        'payment_phone',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
        'validated' => 'boolean',
        'vote_value' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the participation that received this vote
     */
    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    /**
     * Get the participant through participation
     */
    public function participant(): BelongsTo
    {
        return $this->participation->participant();
    }

    /**
     * Scope to get votes from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('voted_at', today());
    }

    /**
     * Scope to get votes from this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('voted_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope to get votes by IP address
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('voter_ip', $ip);
    }
}
