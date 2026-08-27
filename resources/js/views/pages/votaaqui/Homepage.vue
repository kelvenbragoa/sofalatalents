<template>
  <div>
    <!-- Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
      <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <router-link to="/" class="logo d-flex align-items-center">
          <h1 class="">VotaAqui</h1>
        </router-link>
        
        <!-- Navigation Menu -->
        <nav class="d-none d-md-flex">
          <ul class="nav-links d-flex list-unstyled mb-0">
            <li class="me-3">
              <a href="#speakers" @click.prevent="scrollToSection('speakers')" class="nav-link">
                Participantes
              </a>
            </li>
            <!-- <li class="me-3">
              <a href="/homepage-blade" class="nav-link" target="_blank">
                Versão Original
              </a>
            </li> -->
          </ul>
        </nav>
      </div>
    </header>

    <main class="main">
      <!-- Page Title -->
      <div 
        class="page-title dark-background" 
        style="background-image: url('/votaaqui/assets/img/events/karaoke.png'); background-size: cover; background-repeat: no-repeat; background-position: center; margin-top: 60px;"
      >
        <div class="container position-relative">
          <h1 style="color: white;">Reality Show - Estrelas do Liv</h1>
          <p>Vote no teu participante favorito com apenas um clique.</p>
        </div>
      </div>

      <!-- Speakers Section -->
      <section id="speakers" class="speakers section">
        <div class="container">
          <div class="speakers-list">
            <div class="row">
              <!-- Loading state -->
              <div v-if="loading" class="col-12 text-center">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Carregando...</span>
                </div>
              </div>

              <!-- Error state -->
              <div v-else-if="error" class="col-12 text-center">
                <div class="alert alert-danger" role="alert">
                  {{ error }}
                </div>
              </div>

              <!-- Participants list -->
              <div 
                v-else
                v-for="participant in participants" 
                :key="participant.id" 
                class="col-lg-3 speaker-entry"
              >
                <div class="speaker-profile">
                  <div class="speaker-meta">
                    <div class="speaker-photo">
                      <img 
                        :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                        :alt="participant.name" 
                        class="img-fluid"
                        @error="handleImageError"
                      >
                    </div>
                    <div class="speaker-info">
                      <h4>{{ participant.stage_name || participant.name }} </h4>
                    </div>
                  </div>
                  <div class="speaker-details">
                    <p class="speaker-summary">{{ participant.biography || 'Participante do Reality Show' }}</p>
                    <div class="speaker-actions">
                      <router-link 
                        :to="{ name: 'votar', params: { id: participant.id } }"
                        class="profile-btn"
                      >
                        Votar neste participante
                      </router-link>
                    </div>
                  </div>
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
              <span class="">VotaAqui</span>
            </router-link>
            <div class="footer-contact pt-3">
              <p>Rua Principal, 123</p>
              <p>Beira, Sofala, Moçambique</p>
              <p class="mt-3"><strong>Telefone:</strong> <span>+258 123 456 789</span></p>
              <p><strong>Email:</strong> <span>info@votaaqui.com</span></p>
            </div>
            <div class="social-links d-flex mt-4">
              <a href="#"><i class="bi bi-twitter-x"></i></a>
              <a href="#"><i class="bi bi-facebook"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
              <a href="#"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Links Úteis</h4>
            <ul>
              <li><a href="#" @click.prevent="scrollToTop">Início</a></li>
              <li><a href="#speakers" @click.prevent="scrollToSection('speakers')">Participantes</a></li>
              <li><a href="#" @click.prevent>Sobre nós</a></li>
              <li><a href="#" @click.prevent>Termos de serviço</a></li>
              <li><a href="#" @click.prevent>Política de privacidade</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Como Votar</h4>
            <ul>
              <li><a href="#" @click.prevent>Escolha o participante</a></li>
              <li><a href="#" @click.prevent>Clique em votar</a></li>
              <li><a href="#" @click.prevent>Confirme seu voto</a></li>
              <li><a href="#" @click.prevent>Acompanhe os resultados</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Reality Show</h4>
            <ul>
              <li><router-link to="/episodios" @click.prevent>Episódios</router-link></li>
            <li><router-link to="/eliminacoes" @click.prevent>Eliminações</router-link></li>

              <li><router-link to="/proximos-eventos" @click.prevent>Próximos eventos</router-link></li>
              <li><router-link to="/resultados" @click.prevent>Resultados</router-link></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Suporte</h4>
            <ul>
              <li><a href="#" @click.prevent>Ajuda</a></li>
              <li><a href="#" @click.prevent>FAQ</a></li>
              <li><a href="#" @click.prevent>Contacto</a></li>
              <li><a href="#" @click.prevent>Reportar problema</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="">VotaAqui</strong> <span>Todos os direitos reservados</span></p>
      </div>
    </footer>

    <!-- Scroll Top Button -->
    <a 
      v-show="showScrollTop" 
      href="#" 
      @click.prevent="scrollToTop"
      id="scroll-top" 
      class="scroll-top d-flex align-items-center justify-content-center"
    >
      <i class="bi bi-arrow-up-short"></i>
    </a>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Reactive data (only what needs to be dynamic)
const participants = ref([])
const loading = ref(true)
const error = ref(null)
const showScrollTop = ref(false)

// Methods
const fetchParticipants = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/api/participants')
    participants.value = response.data.data || response.data
  } catch (err) {
    console.error('Erro ao carregar participantes:', err)
    error.value = 'Erro ao carregar os participantes. Tente novamente mais tarde.'
    
    // Fallback data for testing
    participants.value = [
      {
        id: 1,
        name: 'Jennifer Walsh',
        stage_name: 'Jennifer Walsh',
        biography: 'Cantor e compositor.',
        photo_url: '/votaaqui/assets/img/events/gate1.jpeg',
        voting_code: 'JW001'
      },
      {
        id: 2,
        name: 'Carlos Silva',
        stage_name: 'Carlos Silva',
        biography: 'Dançarino profissional.',
        photo_url: '/votaaqui/assets/img/events/gate1.jpeg',
        voting_code: 'CS002'
      }
    ]
  } finally {
    loading.value = false
  }
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth'
    })
  }
}

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 100
}

const handleImageError = (event) => {
  event.target.src = '/votaaqui/assets/img/events/gate1.jpeg'
}

// Lifecycle
onMounted(() => {
  fetchParticipants()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* Component-specific styles */
.speakers-page {
  font-family: 'Roboto', sans-serif;
}

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

.logo h1 {
  color: white;
  margin: 0;
  font-size: 1.8rem;
  font-weight: 700;
}

.page-title,
.dark-background {
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

.page-title h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.page-title p {
  font-size: 1.2rem;
  opacity: 0.9;
}

.speakers {
  padding: 80px 0;
}

.speaker-entry {
  margin-bottom: 2rem;
}

.speaker-profile {
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.speaker-profile:hover {
  transform: translateY(-5px);
}

.speaker-photo img {
  width: 100%;
  height: 250px;
  object-fit: cover;
}

.speaker-info {
  padding: 1rem;
  text-align: center;
}

.speaker-info h4 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 600;
}

.speaker-details {
  padding: 0 1rem 1.5rem;
  text-align: center;
}

.speaker-summary {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.profile-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
}

.profile-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
  color: white;
}

.footer {
  background: #2c3e50;
  color: white;
  padding: 3rem 0 1rem;
}

.footer h4 {
  color: white;
  margin-bottom: 1rem;
}

.footer-links ul {
  list-style: none;
  padding: 0;
}

.footer-links li {
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: #bdc3c7;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}

.social-links a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #3498db;
  color: white;
  border-radius: 50%;
  margin-right: 0.5rem;
  transition: background 0.3s ease;
}

.social-links a:hover {
  background: #2980b9;
}

.scroll-top {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #667eea;
  color: white;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  text-decoration: none;
  transition: all 0.3s ease;
  z-index: 1000;
}

.scroll-top:hover {
  background: #5a6fd8;
  color: white;
}

.spinner-border {
  color: #667eea;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .page-title h1 {
    font-size: 2rem;
  }
  
  .page-title p {
    font-size: 1rem;
  }
  
  .speaker-entry {
    margin-bottom: 1.5rem;
  }
}
</style>
