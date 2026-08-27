<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $searchQuery = request('query');
        $status = request('status');

        $episodes = Episode::query()
            ->when(request('query'), function($query, $searchQuery) {
                $query->where('title', 'like', "%{$searchQuery}%")
                      ->orWhere('episode_number', 'like', "%{$searchQuery}%")
                      ->orWhere('description', 'like', "%{$searchQuery}%");
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->withCount(['participants', 'eliminatedParticipants', 'publicVotes', 'judgeVotes'])
            ->orderBy('episode_number', 'desc')
            ->paginate();

        return response()->json($episodes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'episode_number' => 'required|integer|min:1|unique:episodes',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'episode_type' => 'required|in:elimination,finale,special,audition',
            'air_date' => 'required|date',
            'status' => 'required|in:scheduled,live,finished',
            'voting_open' => 'required|boolean',
            'voting_start' => 'nullable|date|before_or_equal:voting_end',
            'voting_end' => 'nullable|date|after_or_equal:voting_start',
            'jury_weight' => 'required|numeric|min:0|max:100',
            'public_weight' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'participant_ids' => 'nullable|array',
            'participant_ids.*' => 'exists:participants,id'
        ]);

        // Validate that jury_weight + public_weight = 100
        if (($request->jury_weight + $request->public_weight) != 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma do peso do júri e peso público deve ser igual a 100%'
            ], 422);
        }

        // Validate voting dates
        if ($request->voting_open && (!$request->voting_start || !$request->voting_end)) {
            return response()->json([
                'success' => false,
                'message' => 'Quando a votação está aberta, as datas de início e fim são obrigatórias'
            ], 422);
        }

        $data = $request->only([
            'episode_number', 'title', 'description', 'episode_type', 'air_date',
            'status', 'voting_open', 'voting_start', 'voting_end', 
            'jury_weight', 'public_weight', 'notes'
        ]);

        // Convert dates
        if ($data['air_date']) {
            $data['air_date'] = Carbon::parse($data['air_date']);
        }
        if ($data['voting_start']) {
            $data['voting_start'] = Carbon::parse($data['voting_start']);
        }
        if ($data['voting_end']) {
            $data['voting_end'] = Carbon::parse($data['voting_end']);
        }

        $episode = Episode::create($data);

        // Attach participants if provided
        if ($request->participant_ids) {
            $episode->participants()->attach($request->participant_ids, [
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $episode->load(['participants', 'eliminatedParticipants']),
            'message' => 'Episódio criado com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $episode = Episode::with([
            'participants', 
            'eliminatedParticipants',
            'publicVotes',
            'judgeVotes'
        ])
        ->withCount(['participants', 'eliminatedParticipants', 'publicVotes', 'judgeVotes'])
        ->find($id);

        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $episode
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $episode = Episode::find($id);
        
        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        $request->validate([
            'episode_number' => [
                'required',
                'integer',
                'min:1'
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'episode_type' => 'required|in:elimination,finale,special,audition',
            'air_date' => 'required|date',
            'status' => 'required|in:scheduled,live,finished',
            'voting_open' => 'required|boolean',
            'voting_start' => 'nullable|date|before_or_equal:voting_end',
            'voting_end' => 'nullable|date|after_or_equal:voting_start',
            'jury_weight' => 'required|numeric|min:0|max:100',
            'public_weight' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'participant_ids' => 'nullable|array',
            'participant_ids.*' => 'exists:participants,id'
        ]);

        // Validate that jury_weight + public_weight = 100
        if (($request->jury_weight + $request->public_weight) != 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma do peso do júri e peso público deve ser igual a 100%'
            ], 422);
        }

        // Validate voting dates
        if ($request->voting_open && (!$request->voting_start || !$request->voting_end)) {
            return response()->json([
                'success' => false,
                'message' => 'Quando a votação está aberta, as datas de início e fim são obrigatórias'
            ], 422);
        }

        // Check if episode is finished and has votes - prevent major changes
        if ($episode->status === 'finished' && ($episode->public_votes_count > 0 || $episode->judge_votes_count > 0)) {
            $restrictedFields = ['episode_type', 'jury_weight', 'public_weight', 'voting_start', 'voting_end'];
            $changedFields = array_intersect($restrictedFields, array_keys($request->all()));
            
            if (!empty($changedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível alterar configurações críticas de um episódio finalizado com votos'
                ], 422);
            }
        }

        $data = $request->only([
            'episode_number', 'title', 'description', 'episode_type', 'air_date',
            'status', 'voting_open', 'voting_start', 'voting_end', 
            'jury_weight', 'public_weight', 'notes'
        ]);

        // Convert dates
        if ($data['air_date']) {
            $data['air_date'] = Carbon::parse($data['air_date']);
        }
        if ($data['voting_start']) {
            $data['voting_start'] = Carbon::parse($data['voting_start']);
        }
        if ($data['voting_end']) {
            $data['voting_end'] = Carbon::parse($data['voting_end']);
        }

        $episode->update($data);

        // Update participants if provided
        if ($request->has('participant_ids')) {
            $episode->participants()->sync($request->participant_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $episode->fresh(['participants', 'eliminatedParticipants']),
            'message' => 'Episódio atualizado com sucesso!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $episode = Episode::withCount(['publicVotes', 'judgeVotes'])->find($id);
        
        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        // Check if episode has votes
        if ($episode->public_votes_count > 0 || $episode->judge_votes_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir episódio que já possui votos'
            ], 400);
        }

        // Check if episode has eliminated participants
        if ($episode->eliminatedParticipants()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir episódio que possui participantes eliminados'
            ], 400);
        }

        // Remove participant relationships
        $episode->participants()->detach();
        
        $episode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Episódio excluído com sucesso!'
        ]);
    }

    /**
     * Toggle episode voting status
     */
    public function toggleVoting(string $id)
    {
        $episode = Episode::find($id);
        
        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        // If enabling voting, ensure dates are set
        if (!$episode->voting_open && (!$episode->voting_start || !$episode->voting_end)) {
            return response()->json([
                'success' => false,
                'message' => 'Para abrir votação, configure primeiro as datas de início e fim'
            ], 422);
        }

        // If enabling voting, close all other episodes' voting first
        // Reality show rule: Only one episode can have active voting at a time
        if (!$episode->voting_open) {
            Episode::where('id', '!=', $id)->update(['voting_open' => false]);
            
            // Also set episode status to live when opening voting
            $episode->update([
                'voting_open' => true,
                'status' => 'live'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $episode->fresh(),
                'message' => 'Votação aberta! Outros episódios foram fechados automaticamente.',
                'closed_episodes' => Episode::where('id', '!=', $id)->where('voting_open', false)->count()
            ]);
        } else {
            // Just close voting for this episode
            $episode->update(['voting_open' => false]);
            
            return response()->json([
                'success' => true,
                'data' => $episode->fresh(),
                'message' => 'Votação fechada!'
            ]);
        }
    }

    /**
     * Change episode status
     */
    public function changeStatus(Request $request, string $id)
    {
        $episode = Episode::find($id);
        
        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:scheduled,live,finished'
        ]);

        $newStatus = $request->status;

        // Business rules for status changes
        if ($newStatus === 'live') {
            // Check if there's already a live episode
            $existingLive = Episode::where('status', 'live')->where('id', '!=', $id)->exists();
            if ($existingLive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Só pode haver um episódio ao vivo por vez'
                ], 422);
            }
        }

        if ($newStatus === 'finished' && $episode->voting_open) {
            // Auto-close voting when finishing episode
            $episode->voting_open = false;
        }

        $episode->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'data' => $episode,
            'message' => "Status alterado para '{$newStatus}' com sucesso!"
        ]);
    }

    /**
     * Get episode statistics and voting results
     */
    public function stats(string $id)
    {
        $episode = Episode::with(['participants', 'eliminatedParticipants'])->find($id);
        
        if (!$episode) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado'
            ], 404);
        }

        // Get voting results
        $votingResults = $episode->getVotingResults();
        
        // Basic statistics
        $totalPublicVotes = $episode->publicVotes()->count();
        $totalJudgeVotes = $episode->judgeVotes()->count();
        $uniqueVoters = $episode->publicVotes()->distinct('ip_address')->count();
        
        // Participant statistics
        $totalParticipants = $episode->participants()->count();
        $eliminatedCount = $episode->eliminatedParticipants()->count();
        $activeParticipants = $totalParticipants - $eliminatedCount;

        // Voting timeline (last 24 hours) - using direct query to avoid GROUP BY issues
        $votingTimeline = collect();
        
        $votes = $episode->publicVotes()
            ->where('public_votes.created_at', '>=', now()->subDay())
            ->get(['public_votes.created_at']);
            
        $timeline = $votes->groupBy(function($vote) {
            return $vote->created_at->format('Y-m-d H:00:00');
        })->map(function($group, $key) {
            return [
                'date' => substr($key, 0, 10),
                'hour' => (int)substr($key, 11, 2),
                'votes' => $group->count()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'episode' => $episode,
                'voting_results' => $votingResults,
                'statistics' => [
                    'total_public_votes' => $totalPublicVotes,
                    'total_judge_votes' => $totalJudgeVotes,
                    'unique_voters' => $uniqueVoters,
                    'total_participants' => $totalParticipants,
                    'eliminated_participants' => $eliminatedCount,
                    'active_participants' => $activeParticipants,
                    'voting_active' => $episode->isVotingActive()
                ],
                'voting_timeline' => $timeline
            ]
        ]);
    }

    /**
     * Get available participants for episode
     */
    public function availableParticipants()
    {
        $participants = Participant::where('active', true)
            ->whereNull('eliminated_episode_id')
            ->orderBy('name')
            ->get(['id', 'name', 'stage_name', 'photo_url']);

        return response()->json([
            'success' => true,
            'data' => $participants
        ]);
    }

        /**
     * Get voting results for a specific episode
     */
    public function results($id)
    {
        try {
            $episode = Episode::with(['participants', 'publicVotes', 'judgeVotes'])->findOrFail($id);
            
            if (!$episode->voting_open) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este episódio não possui votação ativa'
                ], 422);
            }
            
            $results = $episode->getVotingResults();
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar resultados da votação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
