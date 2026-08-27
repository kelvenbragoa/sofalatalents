<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EliminationController extends Controller
{
    /**
     * Processo de eliminação
     */
    public function eliminate(Request $request, Episode $episode): JsonResponse
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'reason' => 'nullable|string|max:500'
        ]);

        // Verificar se o episódio pode ter eliminações
        if ($episode->status !== 'live' && $episode->status !== 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Eliminações só podem ocorrer em episódios ao vivo ou finalizados'
            ], 400);
        }

        // Verificar se os participantes estão ativos
        $participants = Participant::whereIn('id', $request->participant_ids)
            ->where('status', 'active')
            ->get();

        if ($participants->count() !== count($request->participant_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Alguns participantes já foram eliminados ou não existem'
            ], 400);
        }

        try {
            // Realizar eliminação
            $success = $episode->eliminateParticipants($request->participant_ids);

            if (!$success) {
                throw new \Exception('Erro ao processar eliminação');
            }

            // Log da eliminação (opcional)
            foreach ($participants as $participant) {
                Log::info("Participante eliminado: {$participant->name} no episódio {$episode->episode_number}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Eliminação processada com sucesso',
                'data' => [
                    'episode' => $episode->fresh(),
                    'eliminated_participants' => $participants
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar eliminação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverter eliminação (em caso de erro)
     */
    public function revert(Request $request, Episode $episode): JsonResponse
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id'
        ]);

        try {
            // Reverter eliminação
            Participant::whereIn('id', $request->participant_ids)
                ->where('eliminated_episode_id', $episode->id)
                ->update([
                    'eliminated_episode_id' => null,
                    'active' => true,
                    'elimination_date' => null
                ]);

            // Atualizar contador do episódio
            $remainingEliminations = Participant::where('eliminated_episode_id', $episode->id)->count();
            $episode->update(['eliminations' => $remainingEliminations]);

            return response()->json([
                'success' => true,
                'message' => 'Eliminação revertida com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reverter eliminação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simular eliminação baseada em votos
     */
    public function simulate(Episode $episode): JsonResponse
    {
        if (!$episode->isVotingActive() && $episode->status !== 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível simular eliminação para este episódio'
            ], 400);
        }

        $results = $episode->getVotingResults();
        
        // Participantes com menor pontuação (candidatos à eliminação)
        $bottomParticipants = collect($results)
            ->sortBy('total_score')
            ->take(3) // 3 participantes com menor pontuação
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'episode' => $episode,
                'all_results' => $results,
                'bottom_three' => $bottomParticipants,
                'elimination_candidates' => $bottomParticipants->pluck('participant'),
                'voting_summary' => [
                    'total_public_votes' => \App\Models\PublicVote::where('episode_id', $episode->id)->count(),
                    'total_judge_votes' => \App\Models\JudgeVote::where('episode_id', $episode->id)->count(),
                    'public_weight' => $episode->public_weight,
                    'jury_weight' => $episode->jury_weight
                ]
            ]
        ]);
    }
}
