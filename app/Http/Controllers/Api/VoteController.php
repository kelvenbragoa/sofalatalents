<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JudgeVote;
use App\Models\Participant;
use App\Models\PublicVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /**
     * Store a newly created vote.
     */
    public function storejudge(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            // 'judge_id' => 'required',
            'final_score' => 'required',
        ]);

        try {
            // Check if participant is active and not eliminated
            $participant = Participant::where('id', $request->participant_id)
                ->where('active', true)
                ->whereNull('eliminated_episode_id')
                ->first();

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante não está disponível para votação'
                ], 400);
            }
            if ($participant->active == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante não está disponível para votação'
                ], 400);
            }

            // Find current episode with voting open (should be only one, but get the latest)
            $currentEpisode = \App\Models\Episode::getCurrentActiveEpisode();

            if (!$currentEpisode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há votação ativa no momento'
                ], 400);
            }

            // Verificar se a votação está realmente ativa (horário, status, etc.)
            if (!$currentEpisode->isVotingActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A votação não está disponível neste momento'
                ], 400);
            }



            // Find or create participation for this participant in current episode
            $participation = \App\Models\Participation::firstOrCreate([
                'participant_id' => $request->participant_id,
                'episode_id' => $currentEpisode->id,
            ], [
                'status' => 'active'
            ]);


            // Create the vote with payment information
            $vote = JudgeVote::create([
                'participation_id' => $participation->id,
                'judge_id' => Auth::user()->id, // Indicar que é voto pago
                'final_score' => $request->final_score,
                'episode_id' => $currentEpisode->id,
            ]);

            // Update participation vote count
            // $participation->increment('judge_votes');
            $participation->increment('jury_score');

            return response()->json([
                'success' => true,
                'data' => $vote,
                'message' => 'Voto pago registrado com sucesso!',
                'participant' => [
                    'name' => $participant->stage_name ?: $participant->name,
                    'voting_code' => $participant->voting_code
                ],
                'episode' => [
                    'title' => $currentEpisode->title,
                    'episode_number' => $currentEpisode->episode_number
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar voto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'voter_name' => 'nullable|string|max:255',
            'voter_email' => 'nullable|email|max:255',
            'voter_phone' => 'required|string|max:20', // Obrigatório para pagamento
            'voter_ip' => 'nullable|ip',
            'voter_device' => 'nullable|string',
            'newsletter_subscription' => 'nullable|boolean',
            // Campos de pagamento
            'payment_reference' => 'required|string|max:100',
            'payment_amount' => 'required|numeric|min:0',
            'payment_phone' => 'required|string|max:20',
        ]);

        try {
            // Check if participant is active and not eliminated
            $participant = Participant::where('id', $request->participant_id)
                ->where('active', true)
                ->whereNull('eliminated_episode_id')
                ->first();

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante não está disponível para votação'
                ], 400);
            }
            if ($participant->active == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante não está disponível para votação'
                ], 400);
            }

            // Find current episode with voting open (should be only one, but get the latest)
            $currentEpisode = \App\Models\Episode::getCurrentActiveEpisode();

            if (!$currentEpisode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há votação ativa no momento'
                ], 400);
            }

            // Verificar se a votação está realmente ativa (horário, status, etc.)
            if (!$currentEpisode->isVotingActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A votação não está disponível neste momento'
                ], 400);
            }

            // Verificar se o pagamento foi processado (validação adicional)
            if ($request->payment_amount < 50.00) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valor de pagamento insuficiente'
                ], 400);
            }

            // Find or create participation for this participant in current episode
            $participation = \App\Models\Participation::firstOrCreate([
                'participant_id' => $request->participant_id,
                'episode_id' => $currentEpisode->id,
            ], [
                'status' => 'active'
            ]);

            // Get client IP if not provided
            $voterIp = $request->voter_ip ?? $request->ip();

            // Rate limiting disabled - unlimited voting allowed
            // $recentVotes = PublicVote::where('ip_address', $voterIp)
            //     ->where('created_at', '>=', now()->subHour())
            //     ->count();
            //
            // if ($recentVotes >= 10) { // Limit to 10 votes per hour per IP
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Limite de votos por hora excedido. Tente novamente mais tarde.'
            //     ], 429);
            // }

            // Create the vote with payment information
            $vote = PublicVote::create([
                'participation_id' => $participation->id,
                'vote_method' => 'web_paid', // Indicar que é voto pago
                'voter_identifier' => $voterIp,
                'used_code' => $participant->voting_code,
                'vote_value' => $request->payment_amount, // Usar valor do pagamento
                'country' => 'MZ',
                'validated' => true,
                'ip_address' => $voterIp,
                'user_agent' => $request->userAgent(),
                'extra_data' => json_encode([
                    'voter_name' => $request->voter_name,
                    'voter_email' => $request->voter_email,
                    'voter_phone' => $request->voter_phone,
                    'newsletter_subscription' => $request->newsletter_subscription ?? false,
                    'payment_validated' => true,
                    'payment_processed_at' => now()->toISOString()
                ]),
                'voted_at' => now(),
                // Campos específicos de pagamento
                'payment_reference' => $request->payment_reference,
                'payment_amount' => $request->payment_amount,
                'payment_phone' => $request->payment_phone,
            ]);

            // Update participation vote count
            $participation->increment('public_votes');

            return response()->json([
                'success' => true,
                'data' => $vote,
                'message' => 'Voto pago registrado com sucesso!',
                'participant' => [
                    'name' => $participant->stage_name ?: $participant->name,
                    'voting_code' => $participant->voting_code
                ],
                'episode' => [
                    'title' => $currentEpisode->title,
                    'episode_number' => $currentEpisode->episode_number
                ],
                'payment' => [
                    'reference' => $request->payment_reference,
                    'amount' => $request->payment_amount,
                    'phone' => $request->payment_phone,
                    'status' => 'confirmed'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar voto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get voting statistics
     */
    public function statistics()
    {
        try {
            $stats = DB::table('public_votes')
                ->join('participations', 'public_votes.participation_id', '=', 'participations.id')
                ->join('participants', 'participations.participant_id', '=', 'participants.id')
                ->join('episodes', 'participations.episode_id', '=', 'episodes.id')
                ->select('participants.id', 'participants.name', 'participants.stage_name', 
                        'episodes.title as episode_title', 'episodes.episode_number',
                        DB::raw('COUNT(public_votes.id) as vote_count'))
                ->where('participants.active', true)
                ->whereNull('participants.eliminated_episode_id')
                ->where('episodes.voting_open', true)
                ->groupBy('participants.id', 'participants.name', 'participants.stage_name', 
                         'episodes.title', 'episodes.episode_number')
                ->orderBy('vote_count', 'desc')
                ->get();

            $totalVotes = $stats->sum('vote_count');

            // Add percentage
            $stats = $stats->map(function ($stat) use ($totalVotes) {
                $stat->percentage = $totalVotes > 0 ? round(($stat->vote_count / $totalVotes) * 100, 2) : 0;
                return $stat;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'total_votes' => $totalVotes,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get votes for a specific participant
     */
    public function participantVotes($participantId)
    {
        try {
            $participant = Participant::findOrFail($participantId);
            
            // Get current episode with voting open
            $currentEpisode = \App\Models\Episode::where('voting_open', true)
                ->where('status', 'live')
                ->first();

            if (!$currentEpisode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há votação ativa no momento'
                ], 400);
            }

            // Get participation for current episode
            $participation = \App\Models\Participation::where('participant_id', $participantId)
                ->where('episode_id', $currentEpisode->id)
                ->first();

            $voteCount = 0;
            $recentVotes = collect();

            if ($participation) {
                $voteCount = PublicVote::where('participation_id', $participation->id)->count();
                
                $recentVotes = PublicVote::where('participation_id', $participation->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(['ip_address', 'voted_at']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'participant' => $participant,
                    'episode' => $currentEpisode,
                    'vote_count' => $voteCount,
                    'recent_votes' => $recentVotes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar votos do participante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
