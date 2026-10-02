<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import axios from 'axios';

const loading = ref(true);
const error = ref('');
const data = ref(null);

const maxDayVotes = computed(() => {
    const days = data.value?.days || [];
    return Math.max(1, ...days.map((day) => day.votes));
});

const formatNumber = (value) => new Intl.NumberFormat('pt-PT').format(Number(value) || 0);

const formatMoney = (value) => `${formatNumber(value)} MT`;

const formatWhen = (value) => {
    if (!value) return '—';
    return new Intl.DateTimeFormat('pt-PT', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(value));
};

const methodLabel = (method) => {
    if (method === 'staff_cash') return 'Staff';
    if (method === 'site' || !method) return 'Site';
    return method;
};

const votingLabel = computed(() => {
    const episode = data.value?.episode;
    if (!episode) return 'Sem gala';
    if (episode.voting_active) return 'Votação aberta';
    if (episode.voting_open) return 'Votação marcada';
    return 'Votação fechada';
});

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await axios.get('/api/web/dashboard');
        data.value = response.data.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Não foi possível carregar o painel.';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
</script>

<template>
    <div class="dash">
        <header class="dash-head">
            <div>
                <p class="dash-kicker">Sofala Talents</p>
                <h1>{{ data?.episode ? `Gala ${data.episode.number}` : 'Painel' }}</h1>
                <p v-if="data?.episode" class="dash-title">{{ data.episode.title }}</p>
            </div>
            <div class="dash-head-side">
                <span class="dash-status" :class="{ open: data?.episode?.voting_active }">{{ votingLabel }}</span>
                <p v-if="data?.episode?.voting_end" class="dash-until">Até {{ formatWhen(data.episode.voting_end) }}</p>
            </div>
        </header>

        <p v-if="loading" class="dash-note">A carregar o painel...</p>
        <p v-else-if="error" class="dash-note">{{ error }}</p>

        <template v-else-if="data">
            <section class="dash-stats">
                <article>
                    <span>Votos hoje</span>
                    <strong>{{ formatNumber(data.today.votes) }}</strong>
                    <small>{{ formatMoney(data.today.amount) }} · {{ formatNumber(data.today.transactions) }} recibos</small>
                </article>
                <article>
                    <span>Votos da gala</span>
                    <strong>{{ formatNumber(data.totals.votes) }}</strong>
                    <small>{{ formatMoney(data.totals.amount) }}</small>
                </article>
                <article>
                    <span>Participantes activos</span>
                    <strong>{{ formatNumber(data.counts.active_participants) }}</strong>
                    <small>{{ formatNumber(data.counts.eliminated_participants) }} eliminados</small>
                </article>
                <article>
                    <span>Equipa de staff</span>
                    <strong>{{ formatNumber(data.counts.staff) }}</strong>
                    <small>{{ formatNumber(data.counts.episodes) }} galas</small>
                </article>
            </section>

            <section class="dash-grid">
                <article class="dash-card">
                    <header>
                        <h2>Classificação</h2>
                        <RouterLink to="/admin/participants">Participantes</RouterLink>
                    </header>
                    <p v-if="!data.ranking.length" class="dash-empty">Esta gala ainda não tem participantes associados.</p>
                    <ol v-else class="dash-rank">
                        <li v-for="(person, index) in data.ranking" :key="person.id">
                            <span class="dash-place">{{ index + 1 }}</span>
                            <img v-if="person.photo_url" :src="person.photo_url" :alt="person.name">
                            <span v-else class="dash-avatar">{{ person.name.slice(0, 1) }}</span>
                            <div class="dash-person">
                                <strong>{{ person.name }}</strong>
                                <span>{{ person.city || 'Sem cidade' }}</span>
                                <i :style="{ width: `${person.percent}%` }"></i>
                            </div>
                            <div class="dash-score">
                                <strong>{{ formatNumber(person.votes) }}</strong>
                                <span>{{ person.percent }}%</span>
                            </div>
                        </li>
                    </ol>
                </article>

                <div class="dash-stack">
                    <article class="dash-card">
                        <header>
                            <h2>Últimos 7 dias</h2>
                        </header>
                        <div class="dash-days">
                            <div v-for="day in data.days" :key="day.date" class="dash-day">
                                <div class="dash-bar" :style="{ height: `${Math.max(6, (day.votes / maxDayVotes) * 100)}%` }"></div>
                                <span>{{ day.label }}</span>
                                <strong>{{ formatNumber(day.votes) }}</strong>
                            </div>
                        </div>
                        <ul v-if="data.methods.length" class="dash-methods">
                            <li v-for="item in data.methods" :key="item.method">
                                <span>{{ methodLabel(item.method) }}</span>
                                <strong>{{ formatNumber(item.votes) }} votos · {{ formatMoney(item.amount) }}</strong>
                            </li>
                        </ul>
                    </article>

                    <article class="dash-card">
                        <header>
                            <h2>Atalhos</h2>
                        </header>
                        <nav class="dash-links">
                            <RouterLink to="/admin/participants">Participantes</RouterLink>
                            <RouterLink to="/admin/episodes">Episódios</RouterLink>
                            <RouterLink to="/admin/voting">Votação</RouterLink>
                            <RouterLink to="/admin/users">Utilizadores</RouterLink>
                        </nav>
                    </article>
                </div>
            </section>

            <section class="dash-card">
                <header>
                    <h2>Últimos votos</h2>
                    <RouterLink to="/admin/voting">Votação</RouterLink>
                </header>
                <p v-if="!data.recent.length" class="dash-empty">Ainda não há votos nesta gala.</p>
                <table v-else>
                    <thead>
                        <tr>
                            <th>Participante</th>
                            <th>Votante</th>
                            <th>Origem</th>
                            <th>Votos</th>
                            <th>Valor</th>
                            <th>Quando</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="vote in data.recent" :key="vote.id">
                            <td>{{ vote.participant || '—' }}</td>
                            <td>{{ vote.voter_name || vote.staff_name || '—' }}</td>
                            <td>{{ methodLabel(vote.method) }}</td>
                            <td>{{ formatNumber(vote.quantity) }}</td>
                            <td>{{ formatMoney(vote.amount) }}</td>
                            <td>{{ formatWhen(vote.voted_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </template>
    </div>
</template>

<style scoped>
.dash {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    color: #161311;
}

.dash-head,
.dash-card header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.dash-kicker {
    margin: 0;
    font-size: 0.78rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6d665e;
}

h1 {
    margin: 0.2rem 0 0;
    font-size: 2rem;
    font-weight: 600;
}

.dash-title,
.dash-until,
.dash-note,
.dash-empty {
    margin: 0.25rem 0 0;
    color: #6d665e;
}

.dash-status {
    display: inline-block;
    border: 1px solid #d9d3cb;
    border-radius: 999px;
    padding: 0.35rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.dash-status.open {
    border-color: #111;
    background: #111;
    color: #fff;
}

.dash-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.85rem;
}

.dash-stats article,
.dash-card {
    border: 1px solid #e6e0d8;
    border-radius: 14px;
    background: #fff;
}

.dash-stats article {
    padding: 1rem 1.1rem 1.05rem;
}

.dash-stats span,
.dash-person span,
.dash-score span,
.dash-day span {
    display: block;
    color: #6d665e;
    font-size: 0.84rem;
}

.dash-stats strong,
.dash-score strong {
    display: block;
    margin-top: 0.35rem;
    font-size: 1.7rem;
    font-weight: 600;
    line-height: 1.1;
}

.dash-stats small {
    display: block;
    margin-top: 0.45rem;
    color: #6d665e;
}

.dash-grid {
    display: grid;
    grid-template-columns: 1.4fr 0.8fr;
    gap: 0.85rem;
}

.dash-stack {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.dash-card {
    padding: 1rem 1.1rem 1.15rem;
}

.dash-card h2 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 600;
}

.dash-card a {
    color: #111;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
}

.dash-rank {
    list-style: none;
    margin: 1rem 0 0;
    padding: 0;
}

.dash-rank li {
    display: grid;
    grid-template-columns: 1.4rem 42px 1fr auto;
    gap: 0.75rem;
    align-items: center;
    padding: 0.7rem 0;
    border-top: 1px solid #efeae3;
}

.dash-place {
    color: #6d665e;
    font-size: 0.85rem;
}

.dash-rank img,
.dash-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
}

.dash-avatar {
    display: grid;
    place-items: center;
    background: #f3eee7;
    font-weight: 600;
}

.dash-person strong,
.dash-score strong {
    font-size: 0.98rem;
}

.dash-person i {
    display: block;
    height: 4px;
    margin-top: 0.4rem;
    border-radius: 99px;
    background: #161311;
    min-width: 4px;
}

.dash-score {
    text-align: right;
}

.dash-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.4rem;
    height: 140px;
    margin-top: 1rem;
    align-items: end;
}

.dash-day {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    height: 100%;
    gap: 0.3rem;
}

.dash-bar {
    width: 100%;
    border-radius: 6px 6px 2px 2px;
    background: #161311;
}

.dash-day strong {
    font-size: 0.75rem;
}

.dash-methods {
    list-style: none;
    margin: 1rem 0 0;
    padding: 0;
}

.dash-methods li {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding-top: 0.65rem;
    font-size: 0.92rem;
}

.dash-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.6rem;
    margin-top: 1rem;
}

.dash-links a {
    border: 1px solid #e6e0d8;
    border-radius: 10px;
    padding: 0.75rem 0.8rem;
}

table {
    width: 100%;
    margin-top: 0.8rem;
    border-collapse: collapse;
}

th,
td {
    padding: 0.7rem 0.4rem;
    border-top: 1px solid #efeae3;
    text-align: left;
    font-size: 0.92rem;
}

th {
    color: #6d665e;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

@media (max-width: 1100px) {
    .dash-stats,
    .dash-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 720px) {
    .dash-stats,
    .dash-grid,
    .dash-links {
        grid-template-columns: 1fr;
    }

    .dash-head {
        flex-direction: column;
    }

    table {
        display: block;
        overflow-x: auto;
    }
}
</style>
