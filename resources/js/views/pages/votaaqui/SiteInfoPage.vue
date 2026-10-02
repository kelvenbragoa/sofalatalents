<script setup>
import { computed } from 'vue'
import PublicShell from '@/components/votaaqui/PublicShell.vue'

const props = defineProps({
  page: {
    type: String,
    required: true,
  },
})

const pages = {
  sobre: {
    kicker: 'O programa',
    title: 'Sobre o Sofala Talents',
    lead: 'A votação pública oficial do reality show realizado na Beira.',
    blocks: [
      {
        title: 'O que é',
        text: 'Sofala Talents é o reality show de talentos da província de Sofala. O público escolhe quem continua na competição através desta plataforma, a VotaAqui.',
      },
      {
        title: 'Quem aparece para votar',
        text: 'Só entram participantes activos da gala em curso. Quem já foi eliminado deixa de receber votos.',
      },
      {
        title: 'O valor do voto',
        text: 'Cada voto custa 30 meticais. Na semana, o voto é feito aqui. Na noite do espectáculo, o staff também pode registar votos da plateia e do bar, sempre com o mesmo valor.',
      },
    ],
  },
  termos: {
    kicker: 'Condições',
    title: 'Termos de serviço',
    lead: 'Regras de utilização da votação oficial do Sofala Talents.',
    blocks: [
      {
        title: 'Voto pago',
        text: 'Cada voto custa 30 MT. O pagamento é confirmado antes de o voto ser gravado. Um pagamento corresponde a um voto para o participante escolhido.',
      },
      {
        title: 'Quando é possível votar',
        text: 'A votação só está disponível enquanto a gala estiver ao vivo e dentro do horário definido pela organização. Fora desse período o sistema não aceita votos.',
      },
      {
        title: 'Dados do votante',
        text: 'O telemóvel é obrigatório para o pagamento. O nome e o email são opcionais. Ao confirmar, o votante aceita que o voto seja associado ao participante e ao episódio em curso.',
      },
      {
        title: 'Uso do sistema',
        text: 'Esta plataforma destina-se ao público do Sofala Talents. Não é permitido registar votos em nome de outra pessoa sem o pagamento correspondente, nem interferir no funcionamento do sistema.',
      },
    ],
  },
  privacidade: {
    kicker: 'Dados',
    title: 'Política de privacidade',
    lead: 'O que é guardado quando alguém vota no Sofala Talents.',
    blocks: [
      {
        title: 'Informação recolhida',
        text: 'Para registar um voto online guardamos o participante escolhido, o valor pago, a referência do pagamento, o número de telemóvel e, se forem indicados, o nome e o email.',
      },
      {
        title: 'Para que serve',
        text: 'Estes dados confirmam o pagamento, contam o voto na gala e permitem o contacto relacionado com essa votação. Não são usados para outra campanha sem o pedido de subscrição.',
      },
      {
        title: 'Partilha',
        text: 'O pagamento é processado com o serviço de pagamento utilizado na transacção. Os totais de votos podem ser mostrados de forma agregada. Não publicamos o nome nem o número de cada votante.',
      },
      {
        title: 'Contacto',
        text: 'Pedidos sobre dados pessoais podem ser enviados para fernandomassasse158@gmail.com ou para o telefone +258 83 405 6545.',
      },
    ],
  },
  ajuda: {
    kicker: 'Suporte',
    title: 'Ajuda e contacto',
    lead: 'Como votar, perguntas frequentes e onde reportar um problema.',
    blocks: [],
  },
}

const current = computed(() => pages[props.page] || pages.sobre)
</script>

<template>
  <PublicShell>
    <section class="st-hero">
      <div class="container">
        <p class="st-kicker">{{ current.kicker }}</p>
        <h1>{{ current.title }}</h1>
        <p>{{ current.lead }}</p>
      </div>
    </section>

    <section class="st-section">
      <div class="container st-prose">
        <article v-for="block in current.blocks" :key="block.title">
          <h2>{{ block.title }}</h2>
          <p>{{ block.text }}</p>
        </article>

        <template v-if="page === 'ajuda'">
          <article id="como-votar">
            <h2>Como votar</h2>
            <ol>
              <li>Na página inicial, escolha um participante activo.</li>
              <li>Indique o telemóvel usado no pagamento. O nome e o email são opcionais.</li>
              <li>Aceite os termos e confirme o pagamento de 30 MT.</li>
              <li>O voto fica registado na gala que estiver aberta.</li>
            </ol>
          </article>
          <article id="faq">
            <h2>Perguntas frequentes</h2>
            <h3>Quanto custa um voto?</h3>
            <p>30 meticais. Para votar mais do que uma vez, cada voto é pago em separado.</p>
            <h3>Posso votar a qualquer hora?</h3>
            <p>Só enquanto a organização mantiver a votação da gala aberta.</p>
            <h3>Onde vejo o que já aconteceu?</h3>
            <p>Os episódios estão em Episódios. As saídas da competição estão em Eliminações.</p>
          </article>
          <article id="contacto">
            <h2>Contacto</h2>
            <p>Beira, Sofala, Moçambique.</p>
            <p><a href="tel:+258834056545">+258 83 405 6545</a></p>
            <p><a href="mailto:fernandomassasse158@gmail.com">fernandomassasse158@gmail.com</a></p>
          </article>
          <article id="reportar">
            <h2>Reportar um problema</h2>
            <p>Se o pagamento foi feito e o voto não apareceu, envie a referência do pagamento e o nome do participante para o email acima.</p>
          </article>
        </template>
      </div>
    </section>
  </PublicShell>
</template>
