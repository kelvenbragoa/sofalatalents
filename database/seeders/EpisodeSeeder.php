<?php

namespace Database\Seeders;

use App\Models\Episode;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $episodes = [
            [
                'episode_number' => 1,
                'title' => 'Estreia - Apresentação dos Participantes',
                'description' => 'Primeiro episódio onde conhecemos todos os participantes do Reality Show Estrelas do LIV.',
                'episode_type' => 'presentation',
                'air_date' => Carbon::parse('2025-09-01 20:00:00'),
                'eliminations' => 0,
                'status' => 'finished',
                'voting_open' => false,
                'jury_weight' => 0,
                'public_weight' => 100,
                'notes' => 'Episódio de apresentação sem eliminação'
            ],
            [
                'episode_number' => 2,
                'title' => 'Primeira Prova - Talentos Individuais',
                'description' => 'Cada participante mostra seu talento individual. Primeira eliminação da temporada.',
                'episode_type' => 'elimination',
                'air_date' => Carbon::parse('2025-09-08 20:00:00'),
                'eliminations' => 1,
                'status' => 'finished',
                'voting_open' => false,
                'voting_start' => Carbon::parse('2025-09-08 21:00:00'),
                'voting_end' => Carbon::parse('2025-09-09 12:00:00'),
                'jury_weight' => 60,
                'public_weight' => 40,
                'notes' => 'Primeira eliminação baseada em performance individual'
            ],
            [
                'episode_number' => 3,
                'title' => 'Prova em Equipe - Colaboração',
                'description' => 'Participantes trabalham em equipes para superar desafios colaborativos.',
                'episode_type' => 'elimination',
                'air_date' => Carbon::parse('2025-09-15 20:00:00'),
                'eliminations' => 2,
                'status' => 'finished',
                'voting_open' => false,
                'voting_start' => Carbon::parse('2025-09-15 21:00:00'),
                'voting_end' => Carbon::parse('2025-09-16 12:00:00'),
                'jury_weight' => 50,
                'public_weight' => 50,
                'notes' => 'Eliminação dupla - pior equipe'
            ],
            [
                'episode_number' => 4,
                'title' => 'Episódio Especial - Cultura Moçambicana',
                'description' => 'Participantes celebram a rica cultura de Moçambique através de performances temáticas.',
                'episode_type' => 'special',
                'air_date' => Carbon::parse('2025-09-22 20:00:00'),
                'eliminations' => 0,
                'status' => 'finished',
                'voting_open' => false,
                'jury_weight' => 70,
                'public_weight' => 30,
                'special_settings' => [
                    'theme' => 'Cultura Moçambicana',
                    'guest_judges' => ['Artista Local 1', 'Artista Local 2']
                ],
                'notes' => 'Episódio especial sem eliminação'
            ],
            [
                'episode_number' => 5,
                'title' => 'Semifinal - Os Melhores Talentos',
                'description' => 'Semifinal com os participantes restantes. Performances de alto nível.',
                'episode_type' => 'elimination',
                'air_date' => Carbon::parse('2025-09-29 20:00:00'),
                'eliminations' => 2,
                'status' => 'live',
                'voting_open' => true,
                'voting_start' => Carbon::parse('2025-09-29 21:00:00'),
                'voting_end' => Carbon::parse('2025-09-30 12:00:00'),
                'jury_weight' => 50,
                'public_weight' => 50,
                'notes' => 'Semifinal - últimos 6 participantes'
            ],
            [
                'episode_number' => 6,
                'title' => 'Grande Final - Coroação do Vencedor',
                'description' => 'Grande final com os 4 finalistas. A decisão final está nas mãos do público.',
                'episode_type' => 'final',
                'air_date' => Carbon::parse('2025-10-06 20:00:00'),
                'eliminations' => 3,
                'status' => 'scheduled',
                'voting_open' => false,
                'voting_start' => Carbon::parse('2025-10-06 21:30:00'),
                'voting_end' => Carbon::parse('2025-10-06 23:30:00'),
                'jury_weight' => 30,
                'public_weight' => 70,
                'special_settings' => [
                    'finale' => true,
                    'live_voting' => true,
                    'prizes' => [
                        '1st' => 'Contrato de Gravação + 100,000 MZN',
                        '2nd' => '50,000 MZN',
                        '3rd' => '25,000 MZN'
                    ]
                ],
                'notes' => 'Grande Final - votação ao vivo'
            ]
        ];

        foreach ($episodes as $episode) {
            Episode::create($episode);
        }
    }
}
