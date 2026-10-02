<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

const participants = ref([])
const loading = ref(true)
const error = ref(null)

const fetchParticipants = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/api/participants')
    participants.value = response.data.data || response.data
  } catch (err) {
    console.error('Erro ao carregar participantes:', err)
    error.value = 'Não foi possível carregar os participantes. Tente novamente.'
    participants.value = []
  } finally {
    loading.value = false
  }
}

const handleImageError = (event) => {
  event.target.src = '/votaaqui/assets/img/events/gate1.jpeg'
}

onMounted(fetchParticipants)
</script>

<template>
  <PublicShell>
    <section class="st-hero">
      <div class="container">
        <p class="st-kicker">Reality show</p>
        <h1>Sofala Talents</h1>
        <p>Vote no participante da gala. Cada voto custa 30 meticais e conta para quem continua em competição.</p>
      </div>
    </section>

    <section id="participantes" class="st-section">
      <div class="container">
        <div class="st-section-head">
          <p class="st-kicker">Em competição</p>
          <h2>Participantes activos</h2>
          <p>Escolha um nome para votar na gala que estiver aberta.</p>
        </div>

        <div v-if="loading" class="text-center my-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">A carregar...</span>
          </div>
        </div>

        <div v-else-if="error" class="alert" role="alert">
          {{ error }}
        </div>

        <div v-else-if="participants.length === 0" class="empty-state">
          <h3>Ainda não há participantes activos</h3>
          <p>Quando a organização abrir a gala, os nomes aparecem aqui.</p>
        </div>

        <div v-else class="row">
          <div
            v-for="participant in participants"
            :key="participant.id"
            class="col-lg-3 col-md-6 speaker-entry"
          >
            <article class="speaker-profile">
              <div class="speaker-photo">
                <img
                  :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'"
                  :alt="participant.stage_name || participant.name"
                  @error="handleImageError"
                >
              </div>
              <div class="speaker-info">
                <h4>{{ participant.stage_name || participant.name }}</h4>
              </div>
              <div class="speaker-details">
                <p class="speaker-summary">{{ participant.biography || 'Participante do Sofala Talents' }}</p>
                <router-link
                  :to="{ name: 'votar', params: { id: participant.id } }"
                  class="profile-btn"
                >
                  Votar · 30 MT
                </router-link>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
  </PublicShell>
</template>
