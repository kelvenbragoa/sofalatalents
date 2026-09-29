<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Reactive data
const participant = ref(null)
const voteData = ref(null)
const countdown = ref(10)

// Get data from route params/state
onMounted(() => {
  // Recuperar dados do localStorage ou da navegação
  const storedParticipant = localStorage.getItem('votedParticipant')
  const storedVoteData = localStorage.getItem('voteData')
  
  if (storedParticipant) {
    participant.value = JSON.parse(storedParticipant)
  }
  
  if (storedVoteData) {
    voteData.value = JSON.parse(storedVoteData)
  }
  
  // Limpar dados após carregar
  localStorage.removeItem('votedParticipant')
  localStorage.removeItem('voteData')
  
  // Iniciar countdown
  startCountdown()
})

const startCountdown = () => {
  const timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
      router.push('/')
    }
  }, 1000)
}

const goHome = () => {
  router.push('/')
}

const shareVote = () => {
  if (participant.value) {
    const text = `Acabei de votar na ${participant.value.stage_name || participant.value.name} no Reality Show Sofala Talents! 🌟`
    const url = window.location.origin
    
    if (navigator.share) {
      navigator.share({
        title: 'Voto Registrado!',
        text: text,
        url: url
      })
    } else {
      // Fallback para cópia
      navigator.clipboard.writeText(`${text} ${url}`)
      alert('Link copiado para a área de transferência!')
    }
  }
}
</script>

<template>
  <div>
    <!-- Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
      <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <router-link to="/" class="logo d-flex align-items-center">
          <h1 class="sitename">Votaaqui</h1>
        </router-link>
        
        <!-- Navigation Menu -->
        <nav class="d-none d-md-flex">
          <ul class="nav-links d-flex list-unstyled mb-0">
            <li class="me-3">
              <router-link to="/" class="nav-link">
                <i class="bi bi-arrow-left me-1"></i>
                Voltar
              </router-link>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <main class="main">
      <!-- Success Section -->
      <section class="success-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="success-card">
                
                <!-- Success Icon -->
                <div class="success-icon">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
                
                <!-- Success Message -->
                <div class="success-content">
                  <h1 class="success-title">Pagamento e Voto Confirmados! 🎉</h1>
                  <p class="success-subtitle">
                    Obrigado por apoiar financeiramente o Reality Show Sofala Talents
                  </p>
                  
                  <!-- Participant Info -->
                  <div v-if="participant" class="voted-participant">
                    <div class="participant-avatar">
                      <img 
                        :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                        :alt="participant.name"
                        class="img-fluid"
                      >
                    </div>
                    <div class="participant-info">
                      <h3>{{ participant.stage_name || participant.name }}</h3>
                      <p class="voting-code">Código: {{ participant.voting_code }}</p>
                    </div>
                  </div>
                  
                  <!-- Vote Details -->
                  <div class="vote-details">
                    <div class="detail-item">
                      <i class="bi bi-calendar-check"></i>
                      <span>Voto registrado em {{ new Date().toLocaleDateString('pt-BR') }}</span>
                    </div>
                    <div class="detail-item">
                      <i class="bi bi-credit-card-fill"></i>
                      <span>Pagamento: {{ voteData?.payment_amount || '50' }} MT confirmado</span>
                    </div>
                    <div class="detail-item" v-if="voteData?.payment_reference">
                      <i class="bi bi-receipt"></i>
                      <span>Referência: {{ voteData.payment_reference }}</span>
                    </div>
                    <div class="detail-item">
                      <i class="bi bi-shield-check"></i>
                      <span>Voto verificado e validado</span>
                    </div>
                    <div class="detail-item">
                      <i class="bi bi-heart-fill"></i>
                      <span>Seu apoio foi computado</span>
                    </div>
                  </div>
                  
                  <!-- Share Section -->
                  <div class="share-section">
                    <h4>Compartilhe seu voto</h4>
                    <p>Mostre aos seus amigos que você está participando!</p>
                    <button @click="shareVote" class="btn-share">
                      <i class="bi bi-share me-2"></i>
                      Compartilhar
                    </button>
                  </div>
                  
                  <!-- Actions -->
                  <div class="action-buttons">
                    <button @click="goHome" class="btn-primary">
                      <i class="bi bi-house me-2"></i>
                      Voltar ao Início
                    </button>
                    <router-link to="/episodios" class="btn-secondary">
                      <i class="bi bi-tv me-2"></i>
                      Ver Episódios
                    </router-link>
                  </div>
                  
                  <!-- Countdown -->
                  <div class="countdown">
                    <small>
                      Redirecionamento automático em {{ countdown }} segundos...
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Next Steps Section -->
      <section class="next-steps-section">
        <div class="container">
          <div class="row">
            <div class="col-md-4">
              <div class="step-card">
                <div class="step-icon">
                  <i class="bi bi-people"></i>
                </div>
                <h5>Acompanhe outros participantes</h5>
                <p>Vote em outros participantes e acompanhe o desempenho de todos</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="step-card">
                <div class="step-icon">
                  <i class="bi bi-bar-chart"></i>
                </div>
                <h5>Veja as estatísticas</h5>
                <p>Acompanhe os resultados e estatísticas de votação em tempo real</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="step-card">
                <div class="step-icon">
                  <i class="bi bi-bell"></i>
                </div>
                <h5>Fique atualizado</h5>
                <p>Receba notificações sobre novos episódios e resultados</p>
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
              <p>Sofala, Moçambique</p>
              <p class="mt-3"><strong>Telefone:</strong> <span>+258 83 405 6545</span></p>
              <p><strong>Email:</strong> <span>fernandomassasse158@gmail.com</span></p>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Links Úteis</h4>
            <ul>
              <li><router-link to="/">Início</router-link></li>
              <li><a href="#" @click.prevent>Sobre nós</a></li>
              <li><a href="#" @click.prevent>Termos de serviço</a></li>
              <li><a href="#" @click.prevent>Política de privacidade</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="px-1 sitename">VotaAqui</strong> <span>Todos os direitos reservados</span></p>
        <div class="credits">
          Desenvolvido com ❤️ para o Reality Show Sofala Talents
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Header */
.header {
  background-color: rgba(0, 0, 0, 0.9);
  padding: 1rem 0;
}

.nav-links {
  align-items: center;
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

/* Success Section */
.success-section {
  padding: 120px 0 60px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.success-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  color: #2c3e50;
}

.success-icon {
  font-size: 4rem;
  color: #28a745;
  margin-bottom: 1.5rem;
}

.success-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #2c3e50;
}

.success-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  color: #6c757d;
}

/* Voted Participant */
.voted-participant {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin: 2rem 0;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 15px;
}

.participant-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #667eea;
}

.participant-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.participant-info h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.voting-code {
  margin: 0.25rem 0 0;
  color: #667eea;
  font-weight: 600;
  font-size: 1.1rem;
}

/* Vote Details */
.vote-details {
  margin: 2rem 0;
}

.detail-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin: 1rem 0;
  color: #495057;
  font-size: 1.1rem;
}

.detail-item i {
  color: #28a745;
  font-size: 1.2rem;
}

/* Share Section */
.share-section {
  margin: 2rem 0;
  padding: 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  color: white;
}

.share-section h4 {
  margin-bottom: 0.5rem;
}

.btn-share {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  margin-top: 1rem;
}

.btn-share:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin: 2rem 0;
  flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  color: white;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #545b62;
  transform: translateY(-2px);
  color: white;
}

/* Countdown */
.countdown {
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #dee2e6;
  color: #6c757d;
}

/* Next Steps Section */
.next-steps-section {
  padding: 4rem 0;
  background: #f8f9fa;
}

.step-card {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
  transition: transform 0.3s ease;
}

.step-card:hover {
  transform: translateY(-5px);
}

.step-icon {
  font-size: 2.5rem;
  color: #667eea;
  margin-bottom: 1rem;
}

.step-card h5 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.step-card p {
  color: #6c757d;
  margin: 0;
}

/* Footer */
.footer {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
  padding: 3rem 0 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .success-card {
    padding: 2rem 1rem;
  }
  
  .success-title {
    font-size: 2rem;
  }
  
  .voted-participant {
    flex-direction: column;
    text-align: center;
  }
  
  .action-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-primary, .btn-secondary {
    width: 100%;
    max-width: 250px;
  }
}
</style>
