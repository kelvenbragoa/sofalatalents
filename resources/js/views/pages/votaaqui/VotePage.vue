<template>
  <PublicShell>
      <section class="st-hero">
        <div class="container">
          <p class="st-kicker">Votação</p>
          <h1>O seu voto</h1>
          <p>Cada voto custa 50 meticais e fica no participante que escolher.</p>
        </div>
      </section>

      <!-- Vote Form Section -->
      <section id="vote-form" class="buy-tickets section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="ticket-form-wrapper">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center my-5">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Carregando...</span>
                  </div>
                  <p class="mt-3">Carregando informações do participante...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="alert alert-danger" role="alert">
                  <h5>Erro ao carregar participante</h5>
                  <p>{{ error }}</p>
                  <router-link to="/" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-left me-1"></i>
                    Voltar à página inicial
                  </router-link>
                </div>

                <!-- Main Content -->
                <div v-else-if="participant">
                  <!-- Event Info -->
                  <div class="event-info mb-5">
                    
                    <h3>Reality Show - Sofala Talents</h3>
                    <h4 v-if="episode"> <strong>{{episode.title}}</strong></h4>
                    <h4 v-else>Nenhuma gala disponivel</h4>
                    <div class="event-details">
                      <div class="event-meta">
                        <span><i class="bi bi-geo-alt"></i> Beira, Sofala</span>
                        <span><i class="bi bi-cash"></i> 50 MT por voto</span>
                        <span v-if="episode && episode.voting_end"><i class="bi bi-clock"></i> Até {{ formatWhen(episode.voting_end) }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Participant Info -->
                  <div class="ticket-types">
                    <h4>Participante selecionado</h4>
                    
                    <div class="ticket-option selected">
                      <div class="participant-card">
                        <div class="participant-image">
                          <img 
                            :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                            :alt="participant.name"
                            class="img-fluid"
                            @error="handleImageError"
                          >
                        </div>
                        <div class="participant-info">
                          <h5>{{ participant.stage_name || participant.name }}</h5>
                          <p class="participant-bio">{{ participant.biography || 'Participante do Reality Show' }}</p>
                          <div class="participant-details">
                            <span v-if="participant.age">
                              <i class="bi bi-person"></i> {{ participant.age }} anos
                            </span>
                            <span v-if="participant.city">
                              <i class="bi bi-geo-alt"></i> {{ participant.city }}
                            </span>
                            <span v-if="participant.skills">
                              <i class="bi bi-star"></i> {{ participant.skills }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Voting Status Alert -->
                  <div v-if="!votingActive" class="alert alert-warning mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ votingMessage }}
                  </div>

                  <!-- Vote Form -->
                  <form v-if="votingActive" @submit.prevent="submitVote" class="php-email-form ticket-form">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="first_name">Nome *</label>
                          <input 
                            type="text" 
                            v-model="voteForm.firstName"
                            id="first_name" 
                            class="form-control" 
                            placeholder="Seu primeiro nome"
                            required
                            :disabled="submitting"
                          >
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="last_name">Apelido *</label>
                          <input 
                            type="text" 
                            v-model="voteForm.lastName"
                            id="last_name" 
                            class="form-control" 
                            placeholder="Seu apelido"
                            required
                            :disabled="submitting"
                          >
                        </div>
                      </div>
                      
                      <!-- <div class="col-md-6">
                        <div class="form-group">
                          <label for="email">Email</label>
                          <input 
                            type="email" 
                            v-model="voteForm.email"
                            id="email" 
                            class="form-control" 
                            placeholder="seu@email.com"
                            :disabled="submitting"
                          >
                        </div>
                      </div> -->
                      
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="phone">Contacto (Número de Carteira Móvel) *</label>
                          <input 
                            type="number" 
                            v-model="voteForm.phone"
                            id="phone" 
                            class="form-control" 
                            placeholder="841234567"
                            required
                            :disabled="submitting"
                            @input="formatPhoneNumber"
                            pattern="[0-9]{9}"
                            minlength="9"
                            maxlength="9"
                          >
                          <small class="form-text text-muted">
                            Digite apenas números (9 dígitos). Ex: 841234567
                          </small>
                        </div>
                      </div>
                    </div>

                    <!-- Payment and Voting Info -->
                    <div class="payment-info">
                      <div class="tax-note">*Voto com pagamento de 50 MT via carteira móvel</div>
                      <small class="text-muted">O seu voto será contabilizado apenas após confirmação do pagamento.</small>
                    </div>

                    <!-- Terms -->
                    <div class="terms-checkbox">
                      <input 
                        type="checkbox" 
                        id="terms" 
                        v-model="voteForm.acceptTerms"
                        required
                        :disabled="submitting"
                      >
                      <label for="terms">
                        Concordo com os <router-link to="/termos" target="_blank">Termos e Condições</router-link> 
                        e a <router-link to="/privacidade" target="_blank">Política de Privacidade</router-link> *
                      </label>
                    </div>

                    <div class="newsletter-checkbox">
                      <input 
                        type="checkbox" 
                        id="newsletter" 
                        v-model="voteForm.newsletter"
                        :disabled="submitting"
                      >
                      <label for="newsletter">
                        Subscrever newsletter para atualizações do evento
                      </label>
                    </div>

                    <!-- Status Messages -->
                    <div v-if="processingPayment" class="loading">
                      <i class="bi bi-credit-card me-2"></i>
                      Processando pagamento de 50 MT...
                    </div>
                    <div v-else-if="paymentSuccess && submitting" class="loading">
                      <i class="bi bi-check-circle me-2"></i>
                      Pagamento aprovado! Registrando voto...
                    </div>
                    <div v-else-if="submitting" class="loading">
                      <i class="bi bi-arrow-clockwise me-2"></i>
                      Enviando voto...
                    </div>
                    
                    <div v-if="paymentError" class="error-message">
                      <i class="bi bi-exclamation-triangle me-2"></i>
                      {{ submitError }}
                    </div>
                    <div v-else-if="submitError" class="error-message">
                      <i class="bi bi-exclamation-circle me-2"></i>
                      {{ submitError }}
                    </div>
                    
                    <div v-if="submitSuccess" class="sent-message">
                      <i class="bi bi-check-circle-fill me-2"></i>
                      Pagamento e voto confirmados! Redirecionando...
                    </div>

                    <!-- Submit Button -->
                    <div class="form-submit">
                      <button 
                        type="submit" 
                        class="btn-submit"
                        :disabled="submitting || !voteForm.acceptTerms || !participant || !participant.id || !voteForm.phone.trim() || !votingActive"
                      >
                        <i v-if="processingPayment" class="bi bi-credit-card me-2"></i>
                        <i v-else-if="paymentSuccess" class="bi bi-check-circle me-2"></i>
                        <i v-else class="bi bi-cash me-2"></i>
                        
                        <span v-if="processingPayment">Processando Pagamento...</span>
                        <span v-else-if="paymentSuccess">Registrando Voto...</span>
                        <span v-else-if="submitting">Enviando...</span>
                        <span v-else>Pagar 50 MT e Votar</span>
                      </button>
                      
                      <!-- Debug info -->
                      <div v-if="!participant || !participant.id" class="mt-2 text-warning small">
                        ⚠️ Aguardando carregamento dos dados do participante...
                      </div>
                    </div>
                  </form>

                  <!-- Alternative message when voting is not active -->
                  <div v-if="!votingActive" class="text-center py-4">
                    <div class="alert alert-info">
                      <h5><i class="bi bi-info-circle me-2"></i>Votação Indisponível</h5>
                      <p class="mb-0">{{ votingMessage }}</p>
                      <router-link to="/" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-arrow-left me-2"></i>Voltar à Homepage
                      </router-link>
                    </div>
                  </div>
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
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

const route = useRoute()
const router = useRouter()

// Loading states
const loading = ref(false)
const error = ref(null)
const participant = ref(null)

// Vote submission states
const submitting = ref(false)
const submitError = ref(null)
const submitSuccess = ref(false)
const episode = ref(null)

// Voting availability state
const votingActive = ref(false)
const votingMessage = ref('')

// Payment states - novos para sistema de pagamento
const processingPayment = ref(false)
const paymentSuccess = ref(false)
const paymentError = ref(false)
const paymentResponse = ref(null)

// Vote form - agora com validação obrigatória do telefone
const voteForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '', // Agora obrigatório para pagamento
  acceptTerms: false,
  newsletter: false
})

// Check if voting is active
const checkVotingStatus = async () => {
  try {
    const response = await axios.get('/api/votaaqui/episodes/current')
    
    if (response.data.success && response.data.data) {
      episode.value = response.data.data
      votingActive.value = episode.value.voting_open && episode.value.status === 'live'

      if (!votingActive.value) {
        if (!episode.voting_open) {
          votingMessage.value = 'A votação não está aberta no momento.'
        } else if (episode.status !== 'live') {
          votingMessage.value = 'Este episódio não está ativo.'
        } else {
          votingMessage.value = 'A votação não está disponível.'
        }
      }
    } else {
      votingActive.value = false
      votingMessage.value = 'Não há votação ativa no momento.'
    }
  } catch (err) {
    console.error('Error checking voting status:', err)
    votingActive.value = false
    votingMessage.value = 'Erro ao verificar status da votação.'
  }
}

// Fetch participant data
const fetchParticipant = async () => {
  try {
    loading.value = true
    error.value = null
    
    const participantId = route.params.id
    
    if (!participantId) {
      throw new Error('ID do participante não fornecido')
    }
    
    // console.log('Fetching participant with ID:', participantId)
    
    const response = await axios.get(`/api/votaaqui/participants/${participantId}`)
    participant.value = response.data.data
    
    // console.log('Participant loaded:', participant.value)
    
    // Verificar status da votação após carregar o participante
    await checkVotingStatus()
    
  } catch (err) {
    console.error('Error fetching participant:', err)
    error.value = err.response?.data?.message || err.message || 'Erro ao carregar participante'
  } finally {
    loading.value = false
  }
}

// Submit vote function
const submitVote = async () => {
  try {
    submitting.value = true
    submitError.value = null
    submitSuccess.value = false
    paymentError.value = null
    
    // PRIMEIRO: Verificar se a votação está ativa
    if (!votingActive.value) {
      submitError.value = votingMessage.value || 'A votação não está ativa no momento.'
      submitting.value = false
      return
    }
    
    // Debug: verificar se participant está carregado
    // console.log('Participant data:', participant.value)
    // console.log('Participant ID:', participant.value?.id)
    
    if (!participant.value || !participant.value.id) {
      submitError.value = 'Erro: Dados do participante não encontrados. Recarregue a página.'
      submitting.value = false
      return
    }

    // Validar telefone obrigatório
    if (!voteForm.value.phone || voteForm.value.phone.trim() === '') {
      submitError.value = 'Número de telefone é obrigatório para o pagamento.'
      submitting.value = false
      return
    }
    
    // Validar formato do número (9 dígitos)
    const phonePattern = /^[8-9][0-9]{8}$/
    if (!phonePattern.test(voteForm.value.phone)) {
      submitError.value = 'Número de telefone inválido. Digite 9 dígitos começando com 8 ou 9.'
      submitting.value = false
      return
    }
    
    // Etapa 1: Processar pagamento via carteira móvel
    processingPayment.value = true
    submitError.value = 'Processando pagamento de 50 MT...'
    
    const paymentData = {
      amount: 50.00,
      phone: voteForm.value.phone, // Adicionar prefixo +258
      description: `Voto para ${participant.value.stage_name || participant.value.name}`,
      reference: `VOTE_${participant.value.id}`
    }
    
    // console.log('Processing payment:', paymentData)
    
    const paymentResponse = await axios.post('/api/votaaqui/payments/process', paymentData)
    
    if (!paymentResponse.data.success) {
      throw new Error(paymentResponse.data.message || 'Falha no pagamento')
    }
    
    paymentSuccess.value = true
    processingPayment.value = false
    submitError.value = 'Pagamento aprovado! Registrando voto...'
    
    // Etapa 2: Registrar voto após pagamento bem-sucedido
    const voteData = {
      participant_id: participant.value.id,
      voter_name: `${voteForm.value.firstName} ${voteForm.value.lastName}`,
      voter_email: voteForm.value.email || null,
      voter_phone: voteForm.value.phone,
      newsletter_subscription: voteForm.value.newsletter,
      payment_reference: paymentResponse.data.reference,
      payment_amount: 50.00,
      payment_phone: voteForm.value.phone
    }
    
    // console.log('Registering vote with payment:', voteData)
    
    const voteResponse = await axios.post('/api/votaaqui/votes', voteData)
    
    submitSuccess.value = true
    
    // Armazenar dados para a página de sucesso
    localStorage.setItem('votedParticipant', JSON.stringify(participant.value))
    localStorage.setItem('voteData', JSON.stringify({
      voter_name: `${voteForm.value.firstName} ${voteForm.value.lastName}`,
      voter_email: voteForm.value.email,
      voter_phone: `+258${voteForm.value.phone}`,
      payment_amount: 50.00,
      payment_reference: paymentResponse.data.reference,
      voted_at: new Date().toISOString()
    }))
    
    // Redirecionar para página de sucesso após 1 segundo
    setTimeout(() => {
      router.push('/voto-sucesso')
    }, 1000)
    
  } catch (error) {
    console.error('Vote submission error:', error)
    
    if (processingPayment.value) {
      paymentError.value = true
      processingPayment.value = false
      submitError.value = error.response?.data?.message || error.message || 'Erro no pagamento. Tente novamente.'
    } else {
      submitError.value = error.response?.data?.message || error.message || 'Ocorreu um erro ao registrar o voto. Tente novamente.'
    }
    
    submitSuccess.value = false
  } finally {
    submitting.value = false
  }
}

// Handle image error
const handleImageError = (event) => {
  event.target.src = '/votaaqui/assets/img/events/gate1.jpeg'
}

const formatWhen = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleString('pt-PT', {
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Format phone number - only allow numbers
const formatPhoneNumber = (event) => {
  let value = event.target.value
  
  // Remove all non-numeric characters
  value = value.replace(/\D/g, '')
  
  // Limit to 9 digits
  if (value.length > 9) {
    value = value.substring(0, 9)
  }
  
  // Update the form value
  voteForm.value.phone = value
  event.target.value = value
}

// Load participant on component mount
onMounted(() => {
  fetchParticipant()
})
</script>

