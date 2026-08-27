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
              <router-link to="/episodios" class="nav-link">
                <i class="bi bi-tv me-1"></i>
                Episódios
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
          <h1>Histórico de Eliminações</h1>
          <p>Reviva os momentos mais emocionantes do programa</p>
          <nav class="breadcrumbs">
            <ol>
              <li><router-link to="/">Home</router-link></li>
              <li class="current">Eliminações</li>
            </ol>
          </nav>
        </div>
      </div>

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
              <li><a href="#eliminations">Eliminações</a></li>
              <li><a href="#stats">Estatísticas</a></li>
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
import axios from 'axios'

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

.stats {
  padding: 60px 0;
  background: #f8f9fa;
}

.stats-item {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.counter {
  font-size: 3rem;
  font-weight: 700;
  color: #667eea;
  display: block;
}

.current-episode {
  padding: 60px 0;
}

.episode-card {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.episode-card.current {
  border-left: 5px solid #667eea;
}

.episode-number {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  display: inline-block;
  margin-bottom: 1rem;
}

.episode-meta {
  display: flex;
  gap: 1rem;
  margin: 1rem 0;
  flex-wrap: wrap;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
}

.status.live {
  background: #e8f5e8;
  color: #2e7d32;
}

.status.finished {
  background: #f5f5f5;
  color: #666;
}

.status.scheduled {
  background: #fff3e0;
  color: #f57c00;
}

.voting-active {
  background: #ffebee;
  color: #c62828;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.7; }
  100% { opacity: 1; }
}

.voting-info {
  background: #e3f2fd;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1rem;
}

.eliminations {
  padding: 60px 0;
}

.eliminations-timeline {
  max-width: 800px;
  margin: 0 auto;
}

.elimination-episode {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #ff6b6b;
}

.episode-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.elimination-count {
  background: #ffebee;
  color: #c62828;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
}

.eliminated-participants {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.eliminated-participant {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 10px;
}

.participant-photo {
  position: relative;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.participant-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(50%);
}

.elimination-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #c62828;
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
}

.participant-info h4 {
  margin: 0;
  font-size: 1rem;
  color: #2c3e50;
}

.elimination-date {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
}

.no-eliminations {
  text-align: center;
  padding: 3rem;
  color: #666;
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
  .episode-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .episode-meta {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .eliminated-participants {
    grid-template-columns: 1fr;
  }
}
</style>
