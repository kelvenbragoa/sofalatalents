<template>
  <PublicShell>
      <section class="st-hero">
        <div class="container">
          <p class="st-kicker">Competição</p>
          <h1>Eliminações</h1>
          <p>Quem já saiu do Sofala Talents e o episódio em que isso aconteceu.</p>
        </div>
      </section>

      <!-- Statistics Section -->
      <section id="stats" class="stats section">
        <div class="container">
          <div class="row gy-4" v-if="statistics">
            <div class="col-lg-3 col-md-6">
              <div class="stats-item text-center">
                <span class="counter">{{ statistics.total_episodes }}</span>
                <p>Episódios Totais</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-item text-center">
                <span class="counter">{{ statistics.active_participants }}</span>
                <p>Participantes Ativos</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-item text-center">
                <span class="counter">{{ statistics.eliminated_participants }}</span>
                <p>Eliminados</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-item text-center">
                <span class="counter">{{ statistics.total_public_votes }}</span>
                <p>Votos Totais</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Current Episode -->
      <section v-if="currentEpisode" id="current" class="current-episode section">
        <div class="container">
          <div class="section-header">
            <h2>Episódio Atual</h2>
            <p>Acompanhe o que está acontecendo agora</p>
          </div>
          
          <div class="episode-card current">
            <div class="episode-info">
              <div class="episode-number">EP {{ currentEpisode.episode_number }}</div>
              <h3>{{ currentEpisode.title }}</h3>
              <p>{{ currentEpisode.description }}</p>
              
              <div class="episode-meta">
                <span class="status" :class="currentEpisode.status">
                  <i class="bi bi-circle-fill me-1"></i>
                  {{ getStatusText(currentEpisode.status) }}
                </span>
                <span class="air-date">
                  <i class="bi bi-calendar me-1"></i>
                  {{ formatDate(currentEpisode.air_date) }}
                </span>
                <span v-if="votingActive" class="voting-active">
                  <i class="bi bi-heart me-1"></i>
                  Votação Ativa
                </span>
              </div>
              
              <div v-if="votingActive" class="voting-info">
                <p><strong>Voting ativo!</strong> Vote no seu participante favorito.</p>
                <router-link to="/" class="btn btn-primary">
                  <i class="bi bi-heart me-1"></i>
                  Votar Agora
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Elimination History -->
      <section id="eliminations" class="eliminations section">
        <div class="container">
          <div class="section-header">
            <h2>Histórico de Eliminações</h2>
            <p>Todos os momentos de despedida do programa</p>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center my-5">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="alert alert-danger" role="alert">
            {{ error }}
          </div>

          <!-- Elimination Episodes -->
          <div v-else class="eliminations-timeline">
            <div 
              v-for="episode in eliminations" 
              :key="episode.episode_number"
              :id="`episode-${episode.episode_number}`"
              class="elimination-episode"
            >
              <div class="episode-header">
                <div class="episode-number">EP {{ episode.episode_number }}</div>
                <div class="episode-details">
                  <h3>{{ episode.title }}</h3>
                  <p class="air-date">{{ formatDate(episode.air_date) }}</p>
                  <span class="elimination-count">
                    {{ episode.eliminated_count }} 
                    {{ episode.eliminated_count === 1 ? 'Eliminado' : 'Eliminados' }}
                  </span>
                </div>
              </div>

              <!-- Eliminated Participants -->
              <div class="eliminated-participants">
                <div 
                  v-for="participant in episode.eliminated_participants" 
                  :key="participant.id"
                  class="eliminated-participant"
                >
                  <div class="participant-photo">
                    <img 
                      :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                      :alt="participant.name"
                      class="img-fluid"
                    >
                    <div class="elimination-badge">
                      <i class="bi bi-x-circle"></i>
                    </div>
                  </div>
                  <div class="participant-info">
                    <h4>{{ participant.stage_name || participant.name }}</h4>
                    <p class="elimination-date">
                      Eliminado em {{ formatDate(participant.elimination_date) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Eliminations Yet -->
            <div v-if="eliminations.length === 0" class="no-eliminations">
              <i class="bi bi-heart display-1 text-muted"></i>
              <h3>Ainda não houve eliminações</h3>
              <p>Todos os participantes ainda estão na competição!</p>
            </div>
          </div>
        </div>
      </section>
  </PublicShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

// Reactive data
const eliminations = ref([])
const currentEpisode = ref(null)
const statistics = ref(null)
const loading = ref(true)
const error = ref(null)
const votingActive = ref(false)

// Methods
const fetchEliminationHistory = async () => {
  try {
    const response = await axios.get('/api/votaaqui/eliminations/history')
    eliminations.value = response.data.data
  } catch (err) {
    console.error('Erro ao carregar histórico de eliminações:', err)
    error.value = 'Erro ao carregar histórico de eliminações'
  }
}

const fetchCurrentEpisode = async () => {
  try {
    const response = await axios.get('/api/votaaqui/episodes/current')
    currentEpisode.value = response.data.data
    votingActive.value = response.data.voting_active
  } catch (err) {
    console.log('Nenhum episódio ativo encontrado')
  }
}

const fetchStatistics = async () => {
  try {
    const response = await axios.get('/api/votaaqui/statistics')
    statistics.value = response.data.data
  } catch (err) {
    console.error('Erro ao carregar estatísticas:', err)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-PT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusText = (status) => {
  const statusMap = {
    'scheduled': 'Programado',
    'live': 'Ao Vivo',
    'finished': 'Finalizado'
  }
  return statusMap[status] || status
}

// Lifecycle
onMounted(async () => {
  loading.value = true
  try {
    await Promise.all([
      fetchEliminationHistory(),
      fetchCurrentEpisode(),
      fetchStatistics()
    ])
  } finally {
    loading.value = false
  }
})
</script>

