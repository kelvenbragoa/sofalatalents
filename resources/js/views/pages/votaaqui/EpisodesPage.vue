<template>
  <PublicShell>
      <section class="st-hero">
        <div class="container">
          <p class="st-kicker">Calendário</p>
          <h1>Episódios</h1>
          <p>A gala em curso e as datas da temporada.</p>
        </div>
      </section>

      <!-- Current Episode Section -->
      <section v-if="currentEpisode" id="current" class="current-episode section">
        <div class="container">
          <div class="section-header">
            <h2>Episódio Atual</h2>
            <p>O que está acontecendo agora no programa</p>
          </div>
          
          <div class="episode-card current">
            <div class="episode-badge">
              <i class="bi bi-play-circle-fill"></i>
              ATUAL
            </div>
            <div class="episode-content">
              <div class="episode-number">EPISÓDIO {{ currentEpisode.episode_number }}</div>
              <h3>{{ currentEpisode.title }}</h3>
              <p class="episode-description">{{ currentEpisode.description }}</p>
              
              <div class="episode-meta">
                <div class="meta-item">
                  <i class="bi bi-calendar"></i>
                  {{ formatDate(currentEpisode.air_date) }}
                </div>
                <div class="meta-item">
                  <i class="bi bi-clock"></i>
                  {{ formatTime(currentEpisode.air_date) }}
                </div>
                <div class="meta-item status" :class="currentEpisode.status">
                  <i class="bi bi-circle-fill"></i>
                  {{ getStatusText(currentEpisode.status) }}
                </div>
              </div>

              <div v-if="currentEpisode.voting_open" class="voting-info">
                <div class="voting-status active">
                  <i class="bi bi-heart-fill"></i>
                  <span>Votação Ativa</span>
                </div>
                <p>Você pode votar até {{ formatDateTime(currentEpisode.voting_end) }}</p>
                <router-link to="/" class="btn btn-vote">
                  <i class="bi bi-heart me-2"></i>
                  Votar Agora
                </router-link>
              </div>

              <div v-if="currentEpisode.special_settings" class="special-info">
                <h5><i class="bi bi-star-fill me-2"></i>Episódio Especial</h5>
                <div class="special-details">
                  <div v-if="currentEpisode.special_settings.theme" class="special-item">
                    <strong>Tema:</strong> {{ currentEpisode.special_settings.theme }}
                  </div>
                  <div v-if="currentEpisode.special_settings.guest_judges" class="special-item">
                    <strong>Jurados Especiais:</strong> 
                    {{ currentEpisode.special_settings.guest_judges.join(', ') }}
                  </div>
                  <div v-if="currentEpisode.special_settings.finale" class="special-item finale">
                    <i class="bi bi-trophy-fill me-1"></i>
                    <strong>GRANDE FINAL</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Episodes List -->
      <section id="episodes" class="episodes section">
        <div class="container">
          <div class="section-header">
            <h2>Todos os Episódios</h2>
            <p>Cronologia completa da temporada</p>
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

          <!-- Episodes Timeline -->
          <div v-else class="episodes-timeline">
            <div 
              v-for="episode in episodes" 
              :key="episode.id"
              class="episode-item"
              :class="[episode.status, { 'current': isCurrentEpisode(episode) }]"
            >
              <div class="episode-timeline-marker">
                <div class="timeline-dot" :class="episode.status">
                  <i :class="getStatusIcon(episode.status)"></i>
                </div>
                <div class="timeline-line" v-if="!isLastEpisode(episode)"></div>
              </div>

              <div class="episode-content-wrapper">
                <div class="episode-header">
                  <div class="episode-number">EP {{ episode.episode_number }}</div>
                  <div class="episode-type" :class="episode.episode_type">
                    {{ getTypeText(episode.episode_type) }}
                  </div>
                </div>

                <div class="episode-details">
                  <h4>{{ episode.title }}</h4>
                  <p class="episode-description">{{ episode.description }}</p>
                  
                  <div class="episode-info">
                    <div class="info-row">
                      <span class="label">Data de Exibição:</span>
                      <span class="value">{{ formatDateTime(episode.air_date) }}</span>
                    </div>
                    <div v-if="episode.eliminations > 0" class="info-row">
                      <span class="label">Eliminações:</span>
                      <span class="value elimination">
                        <i class="bi bi-x-circle me-1"></i>
                        {{ episode.eliminations }} 
                        {{ episode.eliminations === 1 ? 'participante' : 'participantes' }}
                      </span>
                    </div>
                    <div v-if="episode.voting_start && episode.voting_end" class="info-row">
                      <span class="label">Período de Votação:</span>
                      <span class="value">
                        {{ formatTime(episode.voting_start) }} - {{ formatTime(episode.voting_end) }}
                      </span>
                    </div>
                    <div class="info-row">
                      <span class="label">Peso dos Votos:</span>
                      <span class="value">
                        Público: {{ episode.public_weight }}% | 
                        Júri: {{ episode.jury_weight }}%
                      </span>
                    </div>
                  </div>

                  <!-- Episode Actions -->
                  <div class="episode-actions">
                    <button 
                      v-if="episode.status === 'finished' && episode.eliminations > 0"
                      @click="viewResults(episode)"
                      class="btn btn-results"
                    >
                      <i class="bi bi-bar-chart me-1"></i>
                      Ver Resultados
                    </button>
                    
                    <button 
                      v-if="episode.voting_open"
                      @click="goToVoting"
                      class="btn btn-vote-small"
                    >
                      <i class="bi bi-heart me-1"></i>
                      Votar
                    </button>

                    <span 
                      v-if="episode.status === 'scheduled'"
                      class="scheduled-badge"
                    >
                      <i class="bi bi-calendar-event me-1"></i>
                      Programado
                    </span>
                  </div>

                  <!-- Special Episode Info -->
                  <div v-if="episode.special_settings" class="special-episode-info">
                    <div class="special-badge">
                      <i class="bi bi-star-fill"></i>
                      Episódio Especial
                    </div>
                    <div v-if="episode.special_settings.finale" class="finale-badge">
                      <i class="bi bi-trophy-fill"></i>
                      GRANDE FINAL
                    </div>
                  </div>

                  <!-- Notes -->
                  <div v-if="episode.notes" class="episode-notes">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ episode.notes }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Statistics Section -->
      <section id="stats" class="stats section">
        <div class="container">
          <div class="section-header">
            <h2>Estatísticas da Temporada</h2>
          </div>
          
          <div class="row gy-4" v-if="statistics">
            <div class="col-lg-3 col-md-6">
              <div class="stats-card">
                <div class="stats-icon">
                  <i class="bi bi-tv"></i>
                </div>
                <div class="stats-content">
                  <h3>{{ statistics.total_episodes }}</h3>
                  <p>Episódios Totais</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-card">
                <div class="stats-icon">
                  <i class="bi bi-people"></i>
                </div>
                <div class="stats-content">
                  <h3>{{ statistics.active_participants }}</h3>
                  <p>Participantes Ativos</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-card">
                <div class="stats-icon">
                  <i class="bi bi-heart"></i>
                </div>
                <div class="stats-content">
                  <h3>{{ formatNumber(statistics.total_public_votes) }}</h3>
                  <p>Votos Registrados</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="stats-card">
                <div class="stats-icon">
                  <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stats-content">
                  <h3>{{ formatNumber(statistics.votes_this_week) }}</h3>
                  <p>Votos Esta Semana</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
  </PublicShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

const router = useRouter()

// Reactive data
const episodes = ref([])
const currentEpisode = ref(null)
const statistics = ref(null)
const loading = ref(true)
const error = ref(null)

// Methods
const fetchEpisodes = async () => {
  try {
    const response = await axios.get('/api/votaaqui/episodes')
    episodes.value = response.data.data
  } catch (err) {
    console.error('Erro ao carregar episódios:', err)
    error.value = 'Erro ao carregar episódios'
  }
}

const fetchCurrentEpisode = async () => {
  try {
    const response = await axios.get('/api/votaaqui/episodes/current')
    currentEpisode.value = response.data.data
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

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('pt-PT', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('pt-PT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatNumber = (number) => {
  if (!number) return '0'
  return number.toLocaleString('pt-PT')
}

const getStatusText = (status) => {
  const statusMap = {
    'scheduled': 'Programado',
    'live': 'Ao Vivo',
    'finished': 'Finalizado'
  }
  return statusMap[status] || status
}

const getStatusIcon = (status) => {
  const iconMap = {
    'scheduled': 'bi bi-calendar-event',
    'live': 'bi bi-broadcast',
    'finished': 'bi bi-check-circle-fill'
  }
  return iconMap[status] || 'bi bi-circle'
}

const getTypeText = (type) => {
  const typeMap = {
    'presentation': 'Apresentação',
    'elimination': 'Eliminação',
    'special': 'Especial',
    'final': 'Final'
  }
  return typeMap[type] || type
}

const isCurrentEpisode = (episode) => {
  return currentEpisode.value && episode.id === currentEpisode.value.id
}

const isLastEpisode = (episode) => {
  return episodes.value.indexOf(episode) === episodes.value.length - 1
}

const viewResults = (episode) => {
  // Navigate to results page or show modal
  router.push(`/eliminacoes#episode-${episode.episode_number}`)
}

const goToVoting = () => {
  router.push('/')
}

// Lifecycle
onMounted(async () => {
  loading.value = true
  try {
    await Promise.all([
      fetchEpisodes(),
      fetchCurrentEpisode(),
      fetchStatistics()
    ])
  } finally {
    loading.value = false
  }
})
</script>

