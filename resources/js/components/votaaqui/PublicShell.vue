<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const menuOpen = ref(false)

const links = [
  { key: 'home', label: 'Início', to: '/' },
  { key: 'participants', label: 'Participantes', to: { path: '/', hash: '#participantes' } },
  { key: 'episodes', label: 'Episódios', to: '/episodios' },
  { key: 'eliminations', label: 'Eliminações', to: '/eliminacoes' },
  { key: 'help', label: 'Ajuda', to: '/ajuda' },
]

const isActive = (link) => {
  if (link.key === 'participants') {
    return route.path === '/' && route.hash === '#participantes'
  }
  if (link.key === 'home') {
    return route.path === '/' && route.hash !== '#participantes'
  }
  return route.path === link.to
}

watch(() => route.fullPath, () => {
  menuOpen.value = false
})

const year = computed(() => new Date().getFullYear())
</script>

<template>
  <div class="st-site">
    <header class="st-header">
      <div class="container st-header-bar">
        <router-link to="/" class="st-brand" aria-label="Sofala Talents">
          <img src="/votaaqui/assets/img/logo.png" alt="Sofala Talents">
        </router-link>

        <button
          class="st-menu-button"
          type="button"
          :aria-expanded="menuOpen"
          aria-label="Abrir menu"
          @click="menuOpen = !menuOpen"
        >
          <i :class="menuOpen ? 'bi bi-x-lg' : 'bi bi-list'"></i>
        </button>

        <nav class="st-nav" :class="{ 'is-open': menuOpen }">
          <router-link
            v-for="link in links"
            :key="link.key"
            :to="link.to"
            class="st-nav-link"
            :class="{ 'is-active': isActive(link) }"
          >
            {{ link.label }}
          </router-link>
        </nav>
      </div>
    </header>

    <main>
      <slot />
    </main>

    <footer class="st-footer">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4">
            <p class="st-footer-brand">Sofala Talents</p>
            <p>Plataforma oficial de votação do público. Beira, Sofala, Moçambique.</p>
            <p><a href="tel:+258834056545">+258 83 405 6545</a></p>
            <p><a href="mailto:fernandomassasse158@gmail.com">fernandomassasse158@gmail.com</a></p>
          </div>
          <div class="col-6 col-lg-2">
            <h2>Programa</h2>
            <ul>
              <li><router-link to="/">Início</router-link></li>
              <li><router-link :to="{ path: '/', hash: '#participantes' }">Participantes</router-link></li>
              <li><router-link to="/episodios">Episódios</router-link></li>
              <li><router-link to="/eliminacoes">Eliminações</router-link></li>
            </ul>
          </div>
          <div class="col-6 col-lg-3">
            <h2>Institucional</h2>
            <ul>
              <li><router-link to="/sobre">Sobre o programa</router-link></li>
              <li><router-link to="/termos">Termos de serviço</router-link></li>
              <li><router-link to="/privacidade">Política de privacidade</router-link></li>
              <li><router-link to="/ajuda">Ajuda e contacto</router-link></li>
            </ul>
          </div>
          <div class="col-lg-3">
            <h2>Como votar</h2>
            <ol>
              <li>Escolha o participante activo.</li>
              <li>Pague 30 MT por cada voto.</li>
              <li>Confirme os dados do pagamento.</li>
              <li>Acompanhe episódios e eliminações.</li>
            </ol>
            <router-link to="/ajuda#como-votar" class="st-footer-more">Ver instruções</router-link>
          </div>
        </div>
        <p class="st-copy">© {{ year }} Sofala Talents. Todos os direitos reservados.</p>
      </div>
    </footer>
  </div>
</template>
