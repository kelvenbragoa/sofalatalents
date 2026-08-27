<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EpisodeController extends Controller
{
    /**
     * Lista todos os episódios
     */
    public function index(): JsonResponse
    {
        $episodes = Episode::with(['eliminatedParticipants'])
            ->orderBy('episode_number', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $episodes
        ]);
    }

    /**
     * Mostra um episódio específico
     */
    public function show(Episode $episode): JsonResponse
    {
        $episode->load([
            'participants',
            'eliminatedParticipants',
            'judgeVotes.judge',
            'publicVotes'
        ]);

        return response()->json([
            'success' => true,
            'data' => $episode
        ]);
    }

    /**
     * Episódio atual/ativo
     */
    public function current(): JsonResponse
    {
        $currentEpisode = Episode::where('status', 'live')
            ->orWhere('voting_open', true)
            ->with(['participants', 'eliminatedParticipants'])
            ->first();

        if (!$currentEpisode) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum episódio ativo encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currentEpisode,
            'voting_active' => $currentEpisode->isVotingActive()
        ]);
    }

    /**
     * Resultados de votação de um episódio
     */
    public function results(Episode $episode): JsonResponse
    {
        if ($episode->status !== 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Resultados só disponíveis após o episódio terminar'
            ], 400);
        }

        $results = $episode->getVotingResults();

        return response()->json([
            'success' => true,
            'data' => [
                'episode' => $episode,
                'results' => $results,
                'eliminated' => $episode->eliminatedParticipants
            ]
        ]);
    }

    /**
     * Histórico de eliminações
     */
    public function eliminationHistory(): JsonResponse
    {
        $eliminations = Episode::finished()
            ->with(['eliminatedParticipants'])
            ->where('eliminations', '>', 0)
            ->orderBy('episode_number', 'desc')
            ->get()
            ->map(function ($episode) {
                return [
                    'episode_number' => $episode->episode_number,
                    'title' => $episode->title,
                    'air_date' => $episode->air_date,
                    'eliminated_count' => $episode->eliminations,
                    'eliminated_participants' => $episode->eliminatedParticipants->map(function ($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->name,
                            'stage_name' => $participant->stage_name,
                            'photo_url' => $participant->photo_url,
                            'elimination_date' => $participant->elimination_date
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $eliminations
        ]);
    }

    /**
     * Estatísticas do programa
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_episodes' => Episode::count(),
            'finished_episodes' => Episode::finished()->count(),
            'total_participants' => Participant::count(),
            'active_participants' => Participant::where('active', true)->count(),
            'eliminated_participants' => Participant::where('active', false)->count(),
            'total_public_votes' => \App\Models\PublicVote::count(),
            'votes_this_week' => \App\Models\PublicVote::where('created_at', '>=', now()->startOfWeek())->count(),
            'current_episode' => Episode::live()->first()?->episode_number,
            'next_air_date' => Episode::where('status', 'scheduled')
                ->where('air_date', '>', now())
                ->orderBy('air_date')
                ->first()?->air_date
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
