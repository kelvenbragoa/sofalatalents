<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

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
  <PublicShell>
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
                  <h1 class="success-title">Voto confirmado</h1>
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
              <router-link to="/" class="step-card d-block text-decoration-none">
                <div class="step-icon">
                  <i class="bi bi-people"></i>
                </div>
                <h5>Acompanhe outros participantes</h5>
                <p>Volte à lista e vote noutro participante activo.</p>
              </router-link>
            </div>
            <div class="col-md-4">
              <router-link to="/episodios" class="step-card d-block text-decoration-none">
                <div class="step-icon">
                  <i class="bi bi-calendar-event"></i>
                </div>
                <h5>Veja os episódios</h5>
                <p>Acompanhe a gala em curso e o calendário da temporada.</p>
              </router-link>
            </div>
            <div class="col-md-4">
              <router-link to="/eliminacoes" class="step-card d-block text-decoration-none">
                <div class="step-icon">
                  <i class="bi bi-trophy"></i>
                </div>
                <h5>Resultados</h5>
                <p>Consulte quem já saiu da competição.</p>
              </router-link>
            </div>
          </div>
        </div>
      </section>
  </PublicShell>
</template>

