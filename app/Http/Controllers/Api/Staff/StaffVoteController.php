<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Participant;
use App\Models\Participation;
use App\Models\PublicVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffVoteController extends Controller
{
    public const UNIT_PRICE = 50.00;

    public function participants()
    {
        $episode = Episode::getCurrentActiveEpisode();

        if (!$episode || !$episode->isVotingActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Não há gala com votação activa neste momento',
            ], 400);
        }

        $participantIds = Participation::where('episode_id', $episode->id)
            ->where('status', 'active')
            ->pluck('participant_id');

        $query = Participant::query()
            ->where('active', true)
            ->whereNull('eliminated_episode_id')
            ->orderBy('stage_name')
            ->orderBy('name');

        if ($participantIds->isNotEmpty()) {
            $query->whereIn('id', $participantIds);
        }

        $participants = $query->get([
            'id',
            'name',
            'stage_name',
            'photo_url',
            'voting_code',
            'city',
            'province',
        ]);

        return response()->json([
            'success' => true,
            'unit_price' => self::UNIT_PRICE,
            'episode' => [
                'id' => $episode->id,
                'title' => $episode->title,
                'episode_number' => $episode->episode_number,
            ],
            'data' => $participants,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'quantity' => 'required|integer|min:1|max:10000',
            'voter_name' => 'nullable|string|max:255',
        ]);

        $participant = Participant::where('id', $data['participant_id'])
            ->where('active', true)
            ->whereNull('eliminated_episode_id')
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não está disponível para votação',
            ], 400);
        }

        $episode = Episode::getCurrentActiveEpisode();

        if (!$episode || !$episode->isVotingActive()) {
            return response()->json([
                'success' => false,
                'message' => 'A votação não está disponível neste momento',
            ], 400);
        }

        $allowedIds = Participation::where('episode_id', $episode->id)
            ->where('status', 'active')
            ->pluck('participant_id');

        if ($allowedIds->isNotEmpty() && !$allowedIds->contains($participant->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Participante não está nesta gala',
            ], 400);
        }

        $quantity = (int) $data['quantity'];
        $total = round($quantity * self::UNIT_PRICE, 2);
        $staff = Auth::user();
        $voterName = isset($data['voter_name']) ? trim($data['voter_name']) : '';
        $voterName = $voterName === '' ? null : $voterName;

        try {
            $vote = DB::transaction(function () use ($participant, $episode, $quantity, $total, $staff, $voterName, $request) {
                $participation = Participation::firstOrCreate([
                    'participant_id' => $participant->id,
                    'episode_id' => $episode->id,
                ], [
                    'status' => 'active',
                ]);

                $vote = PublicVote::create([
                    'participation_id' => $participation->id,
                    'vote_method' => 'staff_cash',
                    'voter_identifier' => 'staff:'.$staff->id,
                    'used_code' => $participant->voting_code,
                    'vote_value' => $total,
                    'vote_quantity' => $quantity,
                    'unit_price' => self::UNIT_PRICE,
                    'voter_name' => $voterName,
                    'receipt_number' => $this->receiptNumber(),
                    'staff_user_id' => $staff->id,
                    'country' => 'MZ',
                    'validated' => true,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'extra_data' => json_encode([
                        'source' => 'staff_app',
                        'voter_name' => $voterName,
                        'quantity' => $quantity,
                        'unit_price' => self::UNIT_PRICE,
                        'staff_name' => $staff->name,
                    ]),
                    'voted_at' => now(),
                    'payment_reference' => null,
                    'payment_amount' => $total,
                    'payment_phone' => null,
                ]);

                $vote->payment_reference = $vote->receipt_number;
                $vote->save();

                $participation->increment('public_votes', $quantity);

                return $vote;
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar o voto',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voto registrado com sucesso',
            'data' => $this->receiptPayload($vote->fresh(), $participant, $episode),
        ], 201);
    }

    public function index(Request $request)
    {
        $date = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ])['date'] ?? now()->toDateString();

        $votes = $this->staffVotes($date)->get();

        return response()->json([
            'success' => true,
            'date' => $date,
            'unit_price' => self::UNIT_PRICE,
            'summary' => [
                'transactions' => $votes->count(),
                'votes' => (int) $votes->sum('vote_quantity'),
                'amount' => round((float) $votes->sum('payment_amount'), 2),
            ],
            'data' => $votes->map(fn (PublicVote $vote) => $this->listPayload($vote))->values(),
        ]);
    }

    private function staffVotes(string $date)
    {
        return PublicVote::query()
            ->with(['participation.participant', 'participation.episode', 'staff'])
            ->where('staff_user_id', Auth::id())
            ->where('vote_method', 'staff_cash')
            ->whereDate('voted_at', $date)
            ->orderByDesc('voted_at');
    }

    private function receiptNumber(): string
    {
        do {
            $number = 'ST'.now()->format('ymdHis').random_int(100, 999);
        } while (PublicVote::where('receipt_number', $number)->exists());

        return $number;
    }

    private function receiptPayload(PublicVote $vote, Participant $participant, Episode $episode): array
    {
        return [
            'id' => $vote->id,
            'receipt_number' => $vote->receipt_number,
            'quantity' => (int) $vote->vote_quantity,
            'unit_price' => (float) $vote->unit_price,
            'total' => (float) $vote->payment_amount,
            'voter_name' => $vote->voter_name,
            'participant_name' => $participant->stage_name ?: $participant->name,
            'participant_code' => $participant->voting_code,
            'episode_title' => $episode->title,
            'episode_number' => $episode->episode_number,
            'staff_name' => Auth::user()->name,
            'voted_at' => optional($vote->voted_at)->toIso8601String(),
        ];
    }

    private function listPayload(PublicVote $vote): array
    {
        $participant = $vote->participation?->participant;
        $episode = $vote->participation?->episode;

        return [
            'id' => $vote->id,
            'receipt_number' => $vote->receipt_number,
            'quantity' => (int) $vote->vote_quantity,
            'unit_price' => (float) $vote->unit_price,
            'total' => (float) $vote->payment_amount,
            'voter_name' => $vote->voter_name,
            'participant_name' => $participant ? ($participant->stage_name ?: $participant->name) : '',
            'participant_code' => $participant?->voting_code,
            'episode_title' => $episode?->title,
            'episode_number' => $episode?->episode_number,
            'staff_name' => $vote->staff?->name ?? Auth::user()->name,
            'voted_at' => optional($vote->voted_at)->toIso8601String(),
        ];
    }
}
