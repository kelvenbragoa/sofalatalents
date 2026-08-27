<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $searchQuery = request('query');

        $participants = Participant::query()
            ->when(request('query'), function($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                      ->orWhere('stage_name', 'like', "%{$searchQuery}%")
                      ->orWhere('city', 'like', "%{$searchQuery}%");
            })
            ->with(['eliminatedEpisode'])
            ->orderBy('name', 'asc')
            ->paginate();

        return response()->json($participants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stage_name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:16|max:100',
            'gender' => 'required|in:M,F,Other',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'biography' => 'nullable|string|max:1000',
            'occupation' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'voting_code' => 'required|string|max:10|unique:participants',
            'active' => 'required|in:true,false,1,0,"true","false"',
        ]);

        $data = $request->only([
            'name', 'stage_name', 'age', 'gender', 'city', 'province', 
            'biography', 'occupation', 'instagram', 'facebook', 'tiktok', 'voting_code'
        ]);
        
        // Convert boolean values properly - handle string values from frontend
        $activeValue = $request->input('active');
        $data['active'] = in_array($activeValue, ['true', '1', 1, true], true);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('participants', $filename, 'public');
            $data['photo_url'] = '/storage/' . $path;
        }

        // Generate voting code if not provided
        if (empty($data['voting_code'])) {
            $data['voting_code'] = strtoupper(Str::random(6));
        }

        $participant = Participant::create($data);

        return response()->json([
            'success' => true,
            'data' => $participant->load('eliminatedEpisode'),
            'message' => 'Participante criado com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $participant = Participant::with(['eliminatedEpisode'])
            ->find($id);

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $participant
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $participant = Participant::find($id);
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'stage_name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:16|max:100',
            'gender' => 'required|in:M,F,Other',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'biography' => 'nullable|string|max:1000',
            'occupation' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'voting_code' => 'required|string|max:10|unique:participants,voting_code,' . $id,
            'active' => 'required|in:true,false,1,0,"true","false"',
        ]);

        $data = $request->only([
            'name', 'stage_name', 'age', 'gender', 'city', 'province', 
            'biography', 'occupation', 'instagram', 'facebook', 'tiktok', 'voting_code'
        ]);
        
        // Convert boolean values properly - handle string values from frontend
        $activeValue = $request->input('active');
        $data['active'] = in_array($activeValue, ['true', '1', 1, true], true);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($participant->photo_url && Storage::disk('public')->exists(str_replace('/storage/', '', $participant->photo_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $participant->photo_url));
            }

            $photo = $request->file('photo');
            $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('participants', $filename, 'public');
            $data['photo_url'] = '/storage/' . $path;
        }

        $participant->update($data);

        return response()->json([
            'success' => true,
            'data' => $participant->fresh(['eliminatedEpisode']),
            'message' => 'Participante atualizado com sucesso!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $participant = Participant::find($id);
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado'
            ], 404);
        }

        // Check if participant has votes through participations
        $hasVotes = false;
        foreach ($participant->participations as $participation) {
            if ($participation->publicVotes()->exists() || $participation->judgeVotes()->exists()) {
                $hasVotes = true;
                break;
            }
        }
        
        if ($hasVotes) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir participante que já recebeu votos. Desative-o ao invés de excluir.'
            ], 400);
        }

        // Delete photo if exists
        if ($participant->photo_url && Storage::disk('public')->exists(str_replace('/storage/', '', $participant->photo_url))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $participant->photo_url));
        }

        $participant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Participante excluído com sucesso!'
        ]);
    }

    /**
     * Toggle participant status (active/inactive)
     */
    public function toggleStatus(string $id)
    {
        $participant = Participant::find($id);
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado'
            ], 404);
        }

        $participant->update([
            'active' => !$participant->active
        ]);

        return response()->json([
            'success' => true,
            'data' => $participant,
            'message' => $participant->active ? 'Participante ativado!' : 'Participante desativado!'
        ]);
    }

    /**
     * Get participant statistics
     */
    public function stats(string $id)
    {
        $participant = Participant::with(['eliminatedEpisode'])->find($id);
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não encontrado'
            ], 404);
        }

        // Get participations for this participant
        $participations = $participant->participations()->with(['publicVotes', 'judgeVotes'])->get();
        
        // Calculate total votes through participations
        $totalPublicVotes = 0;
        $totalJudgeVotes = 0;
        
        foreach ($participations as $participation) {
            $totalPublicVotes += $participation->publicVotes()->count();
            $totalJudgeVotes += $participation->judgeVotes()->count();
        }
        
        $totalVotes = $totalPublicVotes + $totalJudgeVotes;
        
        // Get current episode votes - using status 'live' or most recent episode with voting open
        $currentEpisode = Episode::where('status', 'live')
            ->orWhere('voting_open', true)
            ->orderBy('episode_number', 'desc')
            ->first();
            
        $currentEpisodeVotes = 0;
        
        if ($currentEpisode) {
            $currentParticipation = $participations->where('episode_id', $currentEpisode->id)->first();
            if ($currentParticipation) {
                $currentEpisodeVotes = $currentParticipation->publicVotes()->count();
            }
        }

        // Calculate average votes per episode
        $episodesParticipated = $participations->count();
        $averageVotesPerEpisode = $episodesParticipated > 0 ? round($totalVotes / $episodesParticipated, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_votes' => $totalVotes,
                'total_public_votes' => $totalPublicVotes,
                'total_judge_votes' => $totalJudgeVotes,
                'current_episode_votes' => $currentEpisodeVotes,
                'average_votes_per_episode' => $averageVotesPerEpisode,
                'episodes_participated' => $episodesParticipated
            ]
        ]);
    }
}
