<template>
  <div>
    <!-- Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
      <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <router-link to="/" class="logo d-flex align-items-center">
          <h1 class="sitename">Votaaqui</h1>
        </router-link>
        
        <nav class="d-none d-md-flex">
          <ul class="nav-links d-flex list-unstyled mb-0">
            <li class="me-3">
              <router-link to="/" class="nav-link">
                <i class="bi bi-house me-1"></i>
                Início
              </router-link>
            </li>
            <li class="me-3">
              <router-link to="/eliminacoes" class="nav-link">
                <i class="bi bi-x-circle me-1"></i>
                Eliminações
              </router-link>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <main class="main">
      <!-- Page Title -->
      <div class="page-title dark-background" style="background-image: url('/votaaqui/assets/img/events/karaoke.png')">
        <div class="container position-relative">
          <h1>Episódios do Reality Show</h1>
          <p>Acompanhe todos os episódios da temporada</p>
          <nav class="breadcrumbs">
            <ol>
              <li><router-link to="/">Home</router-link></li>
              <li class="current">Episódios</li>
            </ol>
          </nav>
        </div>
      </div>

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
    </main>

    <!-- Footer -->
    <footer id="footer" class="footer position-relative dark-background">
      <div class="container footer-top">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6 footer-about">
            <router-link to="/" class="logo d-flex align-items-center">
              <span class="sitename">VotaAqui</span>
            </router-link>
            <div class="footer-contact pt-3">
              <p>Rua Principal, 123</p>
              <p>Beira, Sofala, Moçambique</p>
              <p class="mt-3"><strong>Telefone:</strong> <span>+258 123 456 789</span></p>
              <p><strong>Email:</strong> <span>info@votaaqui.com</span></p>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Navegação</h4>
            <ul>
              <li><router-link to="/">Início</router-link></li>
              <li><router-link to="/episodios">Episódios</router-link></li>
              <li><router-link to="/eliminacoes">Eliminações</router-link></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="px-1 sitename">VotaAqui</strong> <span>Todos os direitos reservados</span></p>
        <div class="credits">
          Desenvolvido com ❤️ para o Reality Show Estrelas do LIV
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

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

<style scoped>
.header {
  background-color: rgba(0, 0, 0, 0.9);
  padding: 1rem 0;
}

.nav-link {
  color: #bdc3c7;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
  padding: 0.5rem 1rem;
  border-radius: 5px;
}

.nav-link:hover {
  color: #667eea;
  background-color: rgba(102, 126, 234, 0.1);
}

.page-title {
  padding: 120px 0 60px 0;
  background-size: cover;
  background-position: center;
  color: white;
  position: relative;
}

.page-title::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}

.page-title .container {
  position: relative;
  z-index: 1;
  text-align: center;
}

.breadcrumbs ol {
  list-style: none;
  padding: 0;
  margin: 1rem 0 0;
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.breadcrumbs li a {
  color: #667eea;
  text-decoration: none;
}

.current-episode {
  padding: 60px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.episode-card.current {
  background: rgba(255, 255, 255, 0.95);
  color: #333;
  border-radius: 20px;
  padding: 2rem;
  position: relative;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.episode-badge {
  position: absolute;
  top: -15px;
  left: 2rem;
  background: #ff6b6b;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.episode-number {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  display: inline-block;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.episode-meta {
  display: flex;
  gap: 1.5rem;
  margin: 1.5rem 0;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #666;
}

.meta-item.status {
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-weight: 500;
}

.meta-item.status.live {
  background: #e8f5e8;
  color: #2e7d32;
}

.voting-info {
  background: #e3f2fd;
  padding: 1.5rem;
  border-radius: 12px;
  margin-top: 1.5rem;
}

.voting-status.active {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #ffebee;
  color: #c62828;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  margin-bottom: 1rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.8; }
  100% { opacity: 1; }
}

.special-info {
  background: #fff3e0;
  padding: 1.5rem;
  border-radius: 12px;
  margin-top: 1.5rem;
  border-left: 4px solid #ff9800;
}

.special-details {
  margin-top: 1rem;
}

.special-item {
  margin-bottom: 0.5rem;
}

.special-item.finale {
  color: #d32f2f;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
}

.episodes {
  padding: 80px 0;
}

.episodes-timeline {
  max-width: 900px;
  margin: 0 auto;
  position: relative;
}

.episode-item {
  display: flex;
  margin-bottom: 3rem;
  position: relative;
}

.episode-timeline-marker {
  flex-shrink: 0;
  position: relative;
  margin-right: 2rem;
}

.timeline-dot {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  position: relative;
  z-index: 2;
}

.timeline-dot.scheduled {
  background: #f57c00;
}

.timeline-dot.live {
  background: #2e7d32;
  animation: pulse-dot 2s infinite;
}

.timeline-dot.finished {
  background: #1976d2;
}

@keyframes pulse-dot {
  0% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(46, 125, 50, 0); }
  100% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0); }
}

.timeline-line {
  position: absolute;
  top: 60px;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: 60px;
  background: #e0e0e0;
}

.episode-content-wrapper {
  flex: 1;
  background: white;
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.episode-item.current .episode-content-wrapper {
  border: 2px solid #667eea;
  transform: scale(1.02);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.episode-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.episode-type {
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
}

.episode-type.presentation {
  background: #e3f2fd;
  color: #1976d2;
}

.episode-type.elimination {
  background: #ffebee;
  color: #c62828;
}

.episode-type.special {
  background: #fff3e0;
  color: #f57c00;
}

.episode-type.final {
  background: #f3e5f5;
  color: #7b1fa2;
}

.episode-info {
  margin: 1.5rem 0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
  border-bottom: none;
}

.label {
  font-weight: 500;
  color: #666;
}

.value.elimination {
  color: #c62828;
  font-weight: 500;
}

.episode-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}

.btn-vote {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.btn-vote:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  color: white;
}

.btn-results {
  background: #2196f3;
  color: white;
}

.btn-vote-small {
  background: #4caf50;
  color: white;
}

.scheduled-badge {
  background: #fff3e0;
  color: #f57c00;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.special-episode-info {
  margin-top: 1rem;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.special-badge {
  background: #fff3e0;
  color: #f57c00;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.finale-badge {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  animation: pulse 2s infinite;
}

.episode-notes {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1rem;
  font-style: italic;
  color: #666;
}

.stats {
  padding: 60px 0;
  background: #f8f9fa;
}

.stats-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.stats-content h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #2c3e50;
}

.stats-content p {
  margin: 0;
  color: #666;
  font-weight: 500;
}

.footer {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
  padding: 3rem 0 1rem;
  margin-top: 3rem;
}

.spinner-border {
  color: #667eea;
  width: 3rem;
  height: 3rem;
}

/* Responsive */
@media (max-width: 768px) {
  .episode-item {
    flex-direction: column;
  }
  
  .episode-timeline-marker {
    margin-right: 0;
    margin-bottom: 1rem;
    align-self: center;
  }
  
  .timeline-line {
    display: none;
  }
  
  .episode-meta {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .episode-actions {
    flex-direction: column;
  }
  
  .info-row {
    flex-direction: column;
    gap: 0.25rem;
  }
}
</style>
