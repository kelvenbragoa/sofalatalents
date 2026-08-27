<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participants = [
            [
                'name' => 'Jennifer Walsh',
                'stage_name' => 'Jenny',
                'age' => 25,
                'gender' => 'F',
                'city' => 'Beira',
                'province' => 'Sofala',
                'phone' => '+258 823 456 789',
                'email' => 'jenny@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Cantora e compositora talentosa com uma voz única. Começou a cantar aos 12 anos e já participou em vários festivais de música.',
                'skills' => 'Canto, Composição, Piano, Guitarra',
                'social_links' => json_encode([
                    'instagram' => '@jenny_official',
                    'facebook' => 'Jenny Music',
                    'youtube' => 'Jenny Walsh Music'
                ]),
                'voting_code' => 'JENNY1',
                'active' => true,
            ],
            [
                'name' => 'Carlos Silva',
                'stage_name' => 'Carlos',
                'age' => 28,
                'gender' => 'M',
                'city' => 'Chimoio',
                'province' => 'Manica',
                'phone' => '+258 824 567 890',
                'email' => 'carlos@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Dançarino profissional especializado em danças africanas contemporâneas. Formado em artes performativas.',
                'skills' => 'Dança Contemporânea, Hip-Hop, Marrabenta, Coreografia',
                'social_links' => json_encode([
                    'instagram' => '@carlos_dancer',
                    'tiktok' => '@carlosmoves'
                ]),
                'voting_code' => 'CARLOS',
                'active' => true,
            ],
            [
                'name' => 'Maria Santos',
                'stage_name' => 'Mari',
                'age' => 23,
                'gender' => 'F',
                'city' => 'Tete',
                'province' => 'Tete',
                'phone' => '+258 825 678 901',
                'email' => 'maria@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Atriz de teatro e cinema com várias peças em seu currículo. Sonha em ser uma estrela internacional.',
                'skills' => 'Atuação, Teatro, Cinema, Dublagem',
                'social_links' => json_encode([
                    'instagram' => '@mari_actress',
                    'linkedin' => 'Maria Santos Actress'
                ]),
                'voting_code' => 'MARIA1',
                'active' => true,
            ],
            [
                'name' => 'João Pedro',
                'stage_name' => 'JP',
                'age' => 26,
                'gender' => 'M',
                'city' => 'Dondo',
                'province' => 'Sofala',
                'phone' => '+258 826 789 012',
                'email' => 'jp@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Comediante e apresentador. Conhecido pelos seus espetáculos de stand-up comedy e humor inteligente.',
                'skills' => 'Comedy, Apresentação, Improvisação, Escrita',
                'social_links' => json_encode([
                    'instagram' => '@jp_comedy',
                    'youtube' => 'JP Comedy Channel'
                ]),
                'voting_code' => 'JOAO1P',
                'active' => true,
            ],
            [
                'name' => 'Ana Ferreira',
                'stage_name' => 'Aninha',
                'age' => 24,
                'gender' => 'F',
                'city' => 'Maputo',
                'province' => 'Maputo',
                'phone' => '+258 827 890 123',
                'email' => 'ana@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Modelo e influenciadora digital. Apaixonada por moda e estilo de vida saudável.',
                'skills' => 'Modelagem, Fotografia, Redes Sociais, Fitness',
                'social_links' => json_encode([
                    'instagram' => '@aninha_model',
                    'facebook' => 'Ana Ferreira Model',
                    'tiktok' => '@aninha_style'
                ]),
                'voting_code' => 'ANINHA',
                'active' => true,
            ],
            [
                'name' => 'Paulo Costa',
                'stage_name' => 'Paulo',
                'age' => 29,
                'gender' => 'M',
                'city' => 'Nampula',
                'province' => 'Nampula',
                'phone' => '+258 828 901 234',
                'email' => 'paulo@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Chef de cozinha especializado em culinária moçambicana contemporânea. Vencedor de vários concursos gastronómicos.',
                'skills' => 'Culinária, Gastronomia, Criatividade, Gestão',
                'social_links' => json_encode([
                    'instagram' => '@chef_paulo',
                    'youtube' => 'Paulo Costa Chef'
                ]),
                'voting_code' => 'PAULO1',
                'active' => true,
            ],
            [
                'name' => 'Beatriz Lima',
                'stage_name' => 'Bia',
                'age' => 22,
                'gender' => 'F',
                'city' => 'Gorongosa',
                'province' => 'Sofala',
                'phone' => '+258 829 012 345',
                'email' => 'bia@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Estudante de medicina e voluntária. Combina os estudos com a paixão pela música e dança.',
                'skills' => 'Medicina, Canto, Dança, Voluntariado',
                'social_links' => json_encode([
                    'instagram' => '@bia_med',
                    'linkedin' => 'Beatriz Lima'
                ]),
                'voting_code' => 'BIALIM',
                'active' => true,
            ],
            [
                'name' => 'Ricardo Mendes',
                'stage_name' => 'Rick',
                'age' => 27,
                'gender' => 'M',
                'city' => 'Beira',
                'province' => 'Sofala',
                'phone' => '+258 830 123 456',
                'email' => 'rick@example.com',
                'photo_url' => '/votaaqui/assets/img/events/gate1.jpeg',
                'biography' => 'Músico e produtor musical. Toca vários instrumentos e produz música para outros artistas.',
                'skills' => 'Produção Musical, Guitarra, Piano, Bateria',
                'social_links' => json_encode([
                    'instagram' => '@rick_producer',
                    'soundcloud' => 'Rick Mendes',
                    'spotify' => 'Rick Mendes Music'
                ]),
                'voting_code' => 'RICK01',
                'active' => true,
            ]
        ];

        foreach ($participants as $participant) {
            Participant::create($participant);
        }
    }
}
