<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <div class="flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0">Detalhes do Episódio</h5>
                    <div class="flex gap-2">
                        <Button 
                            label="Editar" 
                            icon="pi pi-pencil" 
                            class="p-button-outlined"
                            @click="$router.push(`/admin/episodes/${episode.id}/edit`)"
                            v-if="episode && episode.status !== 'finished'"
                        />
                        <Button 
                            label="Voltar" 
                            icon="pi pi-arrow-left" 
                            class="p-button-outlined"
                            @click="$router.push('/admin/episodes')"
                        />
                    </div>
                </div>

                <div v-if="loading" class="text-center p-4">
                    <ProgressSpinner />
                    <p>Carregando episódio...</p>
                </div>

                <div v-else-if="episode">
                    <!-- Informações Básicas -->
                    <div class="grid mb-4">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <div class="flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="text-primary m-0 mb-2">Episódio #{{ episode.episode_number }}</h6>
                                        <h4 class="m-0 mb-2">{{ episode.title }}</h4>
                                        <Tag 
                                            :value="getStatusLabel(episode.status)" 
                                            :severity="getStatusSeverity(episode.status)"
                                        />
                                    </div>
                                    <div class="text-right">
                                        <Badge 
                                            :value="getTypeLabel(episode.episode_type)" 
                                            class="mr-2"
                                        />
                                        <div class="text-sm text-500 mt-2">
                                            Criado em {{ formatDate(episode.created_at) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="col-12 md:col-6">
                                        <div class="flex align-items-center mb-2">
                                            <i class="pi pi-calendar text-primary mr-2"></i>
                                            <strong>Data de Transmissão:</strong>
                                        </div>
                                        <p class="ml-4 mb-3">{{ formatDateTime(episode.air_date) }}</p>
                                    </div>

                                    <div class="col-12 md:col-6" v-if="episode.voting_open">
                                        <div class="flex align-items-center mb-2">
                                            <i class="pi pi-clock text-primary mr-2"></i>
                                            <strong>Período de Votação:</strong>
                                        </div>
                                        <p class="ml-4 mb-1">
                                            <strong>Início:</strong> {{ formatDateTime(episode.voting_start) }}
                                        </p>
                                        <p class="ml-4 mb-3">
                                            <strong>Fim:</strong> {{ formatDateTime(episode.voting_end) }}
                                        </p>
                                    </div>

                                    <div class="col-12" v-if="episode.description">
                                        <div class="flex align-items-center mb-2">
                                            <i class="pi pi-align-left text-primary mr-2"></i>
                                            <strong>Descrição:</strong>
                                        </div>
                                        <p class="ml-4 mb-3">{{ episode.description }}</p>
                                    </div>

                                    <div class="col-12" v-if="episode.notes">
                                        <div class="flex align-items-center mb-2">
                                            <i class="pi pi-bookmark text-primary mr-2"></i>
                                            <strong>Notas:</strong>
                                        </div>
                                        <p class="ml-4 mb-0">{{ episode.notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configurações de Votação -->
                    <div class="grid mb-4" v-if="episode.voting_open">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Configurações de Votação</h6>
                                
                                <div class="grid">
                                    <div class="col-12 md:col-6">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-users text-4xl text-primary mb-2"></i>
                                            <h3 class="m-0 text-primary">{{ episode.jury_weight }}%</h3>
                                            <p class="m-0 text-600">Peso do Júri</p>
                                        </div>
                                    </div>
                                    <div class="col-12 md:col-6">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-heart text-4xl text-primary mb-2"></i>
                                            <h3 class="m-0 text-primary">{{ episode.public_weight }}%</h3>
                                            <p class="m-0 text-600">Peso Público</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3" v-if="episode.voting_active">
                                    <Message severity="success" class="w-full">
                                        <i class="pi pi-check-circle"></i>
                                        Votação está ativa neste momento
                                    </Message>
                                </div>
                                <div class="mt-3" v-else-if="episode.voting_ended">
                                    <Message severity="info" class="w-full">
                                        <i class="pi pi-info-circle"></i>
                                        Período de votação encerrado
                                    </Message>
                                </div>
                                <div class="mt-3" v-else>
                                    <Message severity="warn" class="w-full">
                                        <i class="pi pi-clock"></i>
                                        Votação ainda não iniciada
                                    </Message>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participantes -->
                    <div class="grid mb-4">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <div class="flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary m-0">Participantes ({{ participants.length }})</h6>
                                    <Button 
                                        label="Gerenciar Participantes"
                                        icon="pi pi-users"
                                        class="p-button-outlined p-button-sm"
                                        @click="$router.push(`/admin/episodes/${episode.id}/edit`)"
                                        v-if="episode.status !== 'finished'"
                                    />
                                </div>

                                <div v-if="participants.length === 0" class="text-center p-4 text-600">
                                    <i class="pi pi-users text-4xl mb-3"></i>
                                    <p>Nenhum participante vinculado a este episódio</p>
                                </div>

                                <div v-else class="grid">
                                    <div 
                                        v-for="participant in participants" 
                                        :key="participant.id"
                                        class="col-12 md:col-6 lg:col-4"
                                    >
                                        <div class="surface-100 p-3 border-round">
                                            <div class="flex align-items-center">
                                                <Avatar 
                                                    :image="participant.photo_url" 
                                                    :label="participant.name.charAt(0)" 
                                                    size="large" 
                                                    shape="circle"
                                                    class="mr-3"
                                                />
                                                <div class="flex-1">
                                                    <h6 class="m-0 mb-1">{{ participant.name }}</h6>
                                                    <p class="m-0 text-600 text-sm">{{ participant.age }} anos</p>
                                                    <Tag 
                                                        v-if="participant.pivot && participant.pivot.eliminated_at"
                                                        value="Eliminado"
                                                        severity="danger"
                                                        class="mt-1"
                                                    />
                                                    <Tag 
                                                        v-else-if="participant.active"
                                                        value="Ativo"
                                                        severity="success"
                                                        class="mt-1"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas de Votação -->
                    <div class="grid" v-if="episode.voting_open && (episode.public_votes_count > 0 || episode.judge_votes_count > 0)">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <div class="flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary m-0">Estatísticas de Votação</h6>
                                    <Button 
                                        label="Atualizar"
                                        icon="pi pi-refresh"
                                        class="p-button-outlined p-button-sm"
                                        @click="loadEpisode"
                                        :loading="loading"
                                    />
                                </div>

                                <div class="grid">
                                    <div class="col-12 md:col-4">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-heart-fill text-4xl text-red-500 mb-2"></i>
                                            <h3 class="m-0 text-900">{{ episode.public_votes_count || 0 }}</h3>
                                            <p class="m-0 text-600">Votos Público</p>
                                        </div>
                                    </div>
                                    <div class="col-12 md:col-4">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-star-fill text-4xl text-yellow-500 mb-2"></i>
                                            <h3 class="m-0 text-900">{{ episode.judge_votes_count || 0 }}</h3>
                                            <p class="m-0 text-600">Votos Júri</p>
                                        </div>
                                    </div>
                                    <div class="col-12 md:col-4">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-chart-bar text-4xl text-green-500 mb-2"></i>
                                            <h3 class="m-0 text-900">{{ (episode.public_votes_count || 0) + (episode.judge_votes_count || 0) }}</h3>
                                            <p class="m-0 text-600">Total de Votos</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4" v-if="episode.status === 'finished'">
                                    <Divider />
                                    <div class="flex justify-content-center">
                                        <Button 
                                            label="Ver Resultados Detalhados"
                                            icon="pi pi-chart-pie"
                                            class="p-button-success"
                                            @click="showResults = true"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center p-4">
                    <Message severity="error" class="w-full">
                        Episódio não encontrado
                    </Message>
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog de Resultados -->
    <Dialog 
        v-model:visible="showResults" 
        :style="{ width: '90vw', maxWidth: '800px' }" 
        header="Resultados da Votação"
        :modal="true"
        class="p-fluid"
    >
        <div v-if="votingResults">
            <div class="grid">
                <div class="col-12" v-for="result in votingResults" :key="result.participant_id">
                    <div class="surface-100 p-3 border-round mb-3">
                        <div class="flex justify-content-between align-items-center">
                            <div class="flex align-items-center">
                                <Avatar 
                                    :image="result.participant_photo" 
                                    :label="result.participant_name.charAt(0)" 
                                    size="large" 
                                    shape="circle"
                                    class="mr-3"
                                />
                                <div>
                                    <h6 class="m-0 mb-1">{{ result.participant_name }}</h6>
                                    <div class="flex gap-2">
                                        <Tag value="Público" severity="info" />
                                        <span>{{ result.public_votes }} votos</span>
                                        <Tag value="Júri" severity="warning" />
                                        <span>{{ result.judge_votes }} votos</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <h4 class="m-0 text-primary">{{ result.total_score.toFixed(2) }}%</h4>
                                <p class="m-0 text-600 text-sm">Score Final</p>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <div class="w-full surface-300 border-round overflow-hidden" style="height: 8px;">
                                <div 
                                    class="bg-primary h-full border-round transition-all transition-duration-300"
                                    :style="{ width: result.total_score + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <template #footer>
            <Button 
                label="Fechar" 
                icon="pi pi-times" 
                @click="showResults = false" 
                class="p-button-outlined"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const route = useRoute()
const toast = useToast()

// Data
const loading = ref(true)
const episode = ref(null)
const participants = ref([])
const showResults = ref(false)
const votingResults = ref(null)

// Methods
const loadEpisode = async () => {
    loading.value = true
    try {
        const response = await axios.get(`/api/web/episodes/${route.params.id}`)
        if (response.data.success) {
            episode.value = response.data.data
            participants.value = episode.value.participants || []
            
            // Se o episódio estiver finalizado, carrega os resultados
            if (episode.value.status === 'finished' && episode.value.voting_open) {
                await loadVotingResults()
            }
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar episódio',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

const loadVotingResults = async () => {
    try {
        const response = await axios.get(`/api/web/episodes/${route.params.id}/results`)
        if (response.data.success) {
            votingResults.value = response.data.data
        }
    } catch (error) {
        console.error('Erro ao carregar resultados:', error)
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR')
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleString('pt-BR')
}

const getStatusLabel = (status) => {
    const labels = {
        'scheduled': 'Agendado',
        'live': 'Ao Vivo',
        'finished': 'Finalizado'
    }
    return labels[status] || status
}

const getStatusSeverity = (status) => {
    const severities = {
        'scheduled': 'info',
        'live': 'success',
        'finished': 'secondary'
    }
    return severities[status] || 'info'
}

const getTypeLabel = (type) => {
    const labels = {
        'elimination': 'Eliminação',
        'finale': 'Final',
        'special': 'Especial',
        'audition': 'Audição'
    }
    return labels[type] || type
}

// Lifecycle
onMounted(() => {
    loadEpisode()
})
</script>

<style scoped>
.card {
    box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}

.surface-card {
    background: var(--surface-card);
}

.text-primary {
    color: var(--primary-color);
}

.surface-100 {
    background: var(--surface-100);
}

.transition-all {
    transition: all 0.3s ease;
}
</style>
