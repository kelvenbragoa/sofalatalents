<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Participant;
use App\Models\PublicVote;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $episode = Episode::getCurrentActiveEpisode()
            ?? Episode::query()->where('status', 'live')->orderByDesc('episode_number')->first()
            ?? Episode::query()->orderByDesc('episode_number')->first();

        $staffRoleId = Role::query()->where('name', 'Staff')->value('id');

        return response()->json([
            'success' => true,
            'data' => [
                'episode' => $episode ? $this->episodePayload($episode) : null,
                'counts' => [
                    'active_participants' => Participant::query()->where('active', true)->whereNull('eliminated_episode_id')->count(),
                    'eliminated_participants' => Participant::query()->where(function ($query) {
                        $query->where('active', false)->orWhereNotNull('eliminated_episode_id');
                    })->count(),
                    'episodes' => Episode::query()->count(),
                    'staff' => $staffRoleId ? User::query()->where('role_id', $staffRoleId)->count() : 0,
                ],
                'totals' => [
                    'votes' => (int) $this->votes($episode)->sum('vote_quantity'),
                    'amount' => round((float) $this->votes($episode)->sum(DB::raw($this->amountExpression())), 2),
                    'transactions' => (int) $this->votes($episode)->count(),
                ],
                'today' => [
                    'votes' => (int) $this->votesOnDate($episode, now()->toDateString())->sum('vote_quantity'),
                    'amount' => round((float) $this->votesOnDate($episode, now()->toDateString())->sum(DB::raw($this->amountExpression())), 2),
                    'transactions' => (int) $this->votesOnDate($episode, now()->toDateString())->count(),
                ],
                'methods' => $this->methods($episode),
                'ranking' => $episode ? $this->ranking($episode) : [],
                'recent' => $this->recent($episode),
                'days' => $this->days($episode),
            ],
        ]);
    }

    private function episodePayload(Episode $episode): array
    {
        return [
            'id' => $episode->id,
            'number' => $episode->episode_number,
            'title' => $episode->title,
            'status' => $episode->status,
            'voting_open' => (bool) $episode->voting_open,
            'voting_active' => $episode->isVotingActive(),
            'voting_start' => optional($episode->voting_start)->toIso8601String(),
            'voting_end' => optional($episode->voting_end)->toIso8601String(),
            'air_date' => optional($episode->air_date)->toIso8601String(),
        ];
    }

    private function votes(?Episode $episode)
    {
        $query = PublicVote::query();

        if ($episode) {
            $query->whereIn('participation_id', function ($sub) use ($episode) {
                $sub->select('id')->from('participations')->where('episode_id', $episode->id);
            });
        }

        return $query;
    }

    private function votesOnDate(?Episode $episode, string $date)
    {
        return $this->votes($episode)->whereRaw('DATE(COALESCE(voted_at, created_at)) = ?', [$date]);
    }

    private function amountExpression(): string
    {
        return 'COALESCE(payment_amount, vote_quantity * COALESCE(unit_price, 30))';
    }

    private function methods(?Episode $episode): array
    {
        return $this->votes($episode)
            ->select('vote_method')
            ->selectRaw('SUM(vote_quantity) as votes')
            ->selectRaw('SUM(' . $this->amountExpression() . ') as amount')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('vote_method')
            ->orderByDesc('votes')
            ->get()
            ->map(function ($row) {
                return [
                    'method' => $row->vote_method ?: 'site',
                    'votes' => (int) $row->votes,
                    'amount' => round((float) $row->amount, 2),
                    'transactions' => (int) $row->transactions,
                ];
            })
            ->values()
            ->all();
    }

    private function ranking(Episode $episode): array
    {
        $rows = DB::table('participations')
            ->join('participants', 'participants.id', '=', 'participations.participant_id')
            ->leftJoin('public_votes', 'public_votes.participation_id', '=', 'participations.id')
            ->where('participations.episode_id', $episode->id)
            ->groupBy('participants.id', 'participants.name', 'participants.stage_name', 'participants.photo_url', 'participants.city')
            ->select([
                'participants.id',
                'participants.name',
                'participants.stage_name',
                'participants.photo_url',
                'participants.city',
                DB::raw('COALESCE(SUM(public_votes.vote_quantity), 0) as votes'),
                DB::raw('COALESCE(SUM(' . $this->amountExpression() . '), 0) as amount'),
            ])
            ->orderByDesc('votes')
            ->orderBy('participants.name')
            ->get();

        $total = (int) $rows->sum('votes');

        return $rows->map(function ($row) use ($total) {
            $votes = (int) $row->votes;

            return [
                'id' => $row->id,
                'name' => $row->stage_name ?: $row->name,
                'city' => $row->city,
                'photo_url' => $row->photo_url,
                'votes' => $votes,
                'amount' => round((float) $row->amount, 2),
                'percent' => $total > 0 ? round(($votes / $total) * 100, 1) : 0,
            ];
        })->values()->all();
    }

    private function recent(?Episode $episode): array
    {
        return $this->votes($episode)
            ->with(['participation.participant:id,name,stage_name', 'staff:id,name'])
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function (PublicVote $vote) {
                $participant = $vote->participation?->participant;

                return [
                    'id' => $vote->id,
                    'participant' => $participant?->stage_name ?: $participant?->name,
                    'voter_name' => $vote->voter_name,
                    'quantity' => (int) $vote->vote_quantity,
                    'amount' => round((float) ($vote->payment_amount ?? ((int) $vote->vote_quantity * (float) ($vote->unit_price ?? 30))), 2),
                    'method' => $vote->vote_method ?: 'site',
                    'staff_name' => $vote->staff?->name,
                    'receipt_number' => $vote->receipt_number,
                    'voted_at' => optional($vote->voted_at ?? $vote->created_at)->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    private function days(?Episode $episode): array
    {
        $labels = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'];
        $from = now()->subDays(6)->startOfDay();

        $rows = $this->votes($episode)
            ->whereRaw('COALESCE(voted_at, created_at) >= ?', [$from])
            ->selectRaw('DATE(COALESCE(voted_at, created_at)) as day')
            ->selectRaw('SUM(vote_quantity) as votes')
            ->selectRaw('SUM(' . $this->amountExpression() . ') as amount')
            ->groupBy(DB::raw('DATE(COALESCE(voted_at, created_at))'))
            ->get()
            ->keyBy(fn ($row) => (string) $row->day);

        return collect(range(0, 6))->map(function ($offset) use ($labels, $rows) {
            $date = now()->subDays(6 - $offset);
            $key = $date->toDateString();
            $row = $rows->get($key);

            return [
                'date' => $key,
                'label' => $labels[$date->dayOfWeek],
                'votes' => (int) ($row->votes ?? 0),
                'amount' => round((float) ($row->amount ?? 0), 2),
            ];
        })->all();
    }
}
