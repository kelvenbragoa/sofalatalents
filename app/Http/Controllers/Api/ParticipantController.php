<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $participants = Participant::where('active', true)
                ->whereNull('eliminated_episode_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $participants
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar participantes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stage_name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:18|max:100',
            'gender' => 'nullable|in:M,F,Other',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo_url' => 'nullable|url',
            'biography' => 'nullable|string',
            'skills' => 'nullable|string',
            'social_links' => 'nullable|string',
        ]);

        try {
            $participant = Participant::create(array_merge(
                $request->all(),
                ['voting_code' => $this->generateVotingCode()]
            ));

            return response()->json([
                'success' => true,
                'data' => $participant,
                'message' => 'Participante criado com sucesso'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar participante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $participant = Participant::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $participant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'stage_name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:18|max:100',
            'gender' => 'nullable|in:M,F,Other',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo_url' => 'nullable|url',
            'biography' => 'nullable|string',
            'skills' => 'nullable|string',
            'social_links' => 'nullable|string',
        ]);

        try {
            $participant = Participant::findOrFail($id);
            $participant->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $participant,
                'message' => 'Participante atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar participante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $participant = Participant::findOrFail($id);
            $participant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Participante removido com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover participante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique voting code for the participant
     */
    private function generateVotingCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (Participant::where('voting_code', $code)->exists());

        return $code;
    }
}
