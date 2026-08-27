<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <div class="flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0">Estatísticas do Episódio</h5>
                    <div class="flex gap-2">
                        <Button 
                            label="Atualizar Dados" 
                            icon="pi pi-refresh" 
                            class="p-button-outlined"
                            @click="loadStats"
                            :loading="loading"
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
                    <p>Carregando estatísticas...</p>
                </div>

                <div v-else-if="episode">
                    <!-- Informações do Episódio -->
                    <div class="grid mb-4">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <div class="flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="text-primary m-0 mb-2">Episódio #{{ episode.episode_number }}</h6>
                                        <h4 class="m-0 mb-2">{{ episode.title }}</h4>
                                        <div class="flex gap-2 align-items-center">
                                            <Tag 
                                                :value="getStatusLabel(episode.status)" 
                                                :severity="getStatusSeverity(episode.status)"
                                            />
                                            <Badge 
                                                :value="getTypeLabel(episode.episode_type)" 
                                                class="mr-2"
                                            />
                                            <Tag 
                                                v-if="episode.voting_open"
                                                value="Votação Ativa" 
                                                severity="success"
                                            />
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-500">
                                            Transmitido em {{ formatDate(episode.air_date) }}
                                        </div>
                                        <div v-if="episode.voting_open" class="text-sm text-500 mt-1">
                                            Votação: {{ formatDateTime(episode.voting_start) }} - {{ formatDateTime(episode.voting_end) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo Estatístico -->
                    <div class="grid mb-4">
                        <div class="col-12 md:col-3">
                            <div class="surface-card p-3 border-round text-center">
                                <i class="pi pi-users text-4xl text-blue-500 mb-2"></i>
                                <h3 class="m-0 text-900">{{ stats.total_participants || 0 }}</h3>
                                <p class="m-0 text-600">Participantes</p>
                            </div>
                        </div>
                        <div class="col-12 md:col-3">
                            <div class="surface-card p-3 border-round text-center">
                                <i class="pi pi-heart-fill text-4xl text-red-500 mb-2"></i>
                                <h3 class="m-0 text-900">{{ stats.total_public_votes || 0 }}</h3>
                                <p class="m-0 text-600">Votos Público</p>
                            </div>
                        </div>
                        <div class="col-12 md:col-3">
                            <div class="surface-card p-3 border-round text-center">
                                <i class="pi pi-star-fill text-4xl text-yellow-500 mb-2"></i>
                                <h3 class="m-0 text-900">{{ stats.total_judge_votes || 0 }}</h3>
                                <p class="m-0 text-600">Votos Júri</p>
                            </div>
                        </div>
                        <div class="col-12 md:col-3">
                            <div class="surface-card p-3 border-round text-center">
                                <i class="pi pi-eye text-4xl text-green-500 mb-2"></i>
                                <h3 class="m-0 text-900">{{ stats.unique_voters || 0 }}</h3>
                                <p class="m-0 text-600">Espectadores Únicos</p>
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
                                            <i class="pi pi-users text-3xl text-primary mb-2"></i>
                                            <h3 class="m-0 text-primary">{{ episode.jury_weight }}%</h3>
                                            <p class="m-0 text-600">Peso do Júri</p>
                                        </div>
                                    </div>
                                    <div class="col-12 md:col-6">
                                        <div class="text-center p-3 border-round surface-100">
                                            <i class="pi pi-heart text-3xl text-primary mb-2"></i>
                                            <h3 class="m-0 text-primary">{{ episode.public_weight }}%</h3>
                                            <p class="m-0 text-600">Peso Público</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resultados de Votação -->
                    <div class="grid mb-4" v-if="votingResults && votingResults.length > 0">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Resultados da Votação</h6>
                                
                                <div class="grid">
                                    <div 
                                        v-for="(result, index) in votingResults" 
                                        :key="result.participant_id"
                                        class="col-12"
                                    >
                                        <div class="surface-100 p-3 border-round mb-3">
                                            <div class="flex justify-content-between align-items-center">
                                                <div class="flex align-items-center">
                                                    <div class="flex align-items-center justify-content-center bg-primary text-white border-round mr-3" 
                                                         style="width: 30px; height: 30px; font-weight: bold;">
                                                        {{ index + 1 }}
                                                    </div>
                                                    <Avatar 
                                                        :image="result.participant_photo" 
                                                        :label="result.participant_name.charAt(0)" 
                                                        size="large" 
                                                        shape="circle"
                                                        class="mr-3"
                                                    />
                                                    <div>
                                                        <h6 class="m-0 mb-1">{{ result.participant_name }}</h6>
                                                        <div class="flex gap-3 text-sm">
                                                            <span>
                                                                <i class="pi pi-heart text-red-500 mr-1"></i>
                                                                {{ result.public_votes }} votos público
                                                            </span>
                                                            <span>
                                                                <i class="pi pi-star text-yellow-500 mr-1"></i>
                                                                {{ result.judge_votes }} votos júri
                                                            </span>
                                                            <span>
                                                                <i class="pi pi-chart-bar text-blue-500 mr-1"></i>
                                                                Média: {{ result.judge_average.toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <h4 class="m-0 text-primary">{{ result.total_score.toFixed(2) }}%</h4>
                                                    <p class="m-0 text-600 text-sm">Score Final</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Barra de Progresso -->
                                            <div class="mt-3">
                                                <div class="flex justify-content-between text-sm mb-1">
                                                    <span>Progresso de Votação</span>
                                                    <span>{{ result.total_score.toFixed(1) }}%</span>
                                                </div>
                                                <div class="w-full surface-300 border-round overflow-hidden" style="height: 8px;">
                                                    <div 
                                                        class="h-full border-round transition-all transition-duration-300"
                                                        :class="index === 0 ? 'bg-green-500' : index === 1 ? 'bg-yellow-500' : index === 2 ? 'bg-orange-500' : 'bg-primary'"
                                                        :style="{ width: Math.min(result.total_score, 100) + '%' }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Timeline de Votos -->
                    <div class="grid mb-4" v-if="votingTimeline && votingTimeline.length > 0">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Timeline de Votação (Últimas 24h)</h6>
                                <Chart type="line" :data="timelineChartData" :options="timelineChartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Análise por Participante -->
                    <div class="grid mb-4" v-if="participantStats && participantStats.length > 0">
                        <div class="col-12">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Análise Detalhada por Participante</h6>
                                
                                <DataTable 
                                    :value="participantStats" 
                                    :paginator="true" 
                                    :rows="10"
                                    dataKey="participant_id"
                                    :rowHover="true"
                                    filterDisplay="menu"
                                    :loading="loading"
                                    :globalFilterFields="['participant_name']"
                                    responsiveLayout="scroll"
                                >
                                    <template #header>
                                        <div class="flex justify-content-between">
                                            <Button type="button" icon="pi pi-filter-slash" label="Limpar" class="p-button-outlined" @click="clearFilter()" />
                                            <span class="p-input-icon-left">
                                                <i class="pi pi-search" />
                                                <InputText v-model="filters['global'].value" placeholder="Buscar participante..." />
                                            </span>
                                        </div>
                                    </template>

                                    <Column field="participant_name" header="Participante" :sortable="true">
                                        <template #body="slotProps">
                                            <div class="flex align-items-center">
                                                <Avatar 
                                                    :image="slotProps.data.participant_photo" 
                                                    :label="slotProps.data.participant_name.charAt(0)" 
                                                    size="normal" 
                                                    shape="circle"
                                                    class="mr-2"
                                                />
                                                {{ slotProps.data.participant_name }}
                                            </div>
                                        </template>
                                    </Column>

                                    <Column field="public_votes" header="Votos Público" :sortable="true">
                                        <template #body="slotProps">
                                            <div class="flex align-items-center">
                                                <i class="pi pi-heart text-red-500 mr-2"></i>
                                                {{ slotProps.data.public_votes }}
                                            </div>
                                        </template>
                                    </Column>

                                    <Column field="judge_votes" header="Votos Júri" :sortable="true">
                                        <template #body="slotProps">
                                            <div class="flex align-items-center">
                                                <i class="pi pi-star text-yellow-500 mr-2"></i>
                                                {{ slotProps.data.judge_votes }}
                                            </div>
                                        </template>
                                    </Column>

                                    <Column field="judge_average" header="Média Júri" :sortable="true">
                                        <template #body="slotProps">
                                            <Badge 
                                                :value="slotProps.data.judge_average.toFixed(2)" 
                                                :severity="slotProps.data.judge_average >= 8 ? 'success' : slotProps.data.judge_average >= 6 ? 'warning' : 'danger'"
                                            />
                                        </template>
                                    </Column>

                                    <Column field="total_score" header="Score Final" :sortable="true">
                                        <template #body="slotProps">
                                            <div class="flex align-items-center">
                                                <ProgressBar 
                                                    :value="Math.min(slotProps.data.total_score, 100)" 
                                                    class="mr-2 flex-1"
                                                    style="height: 0.5rem"
                                                />
                                                <span class="font-bold">{{ slotProps.data.total_score.toFixed(1) }}%</span>
                                            </div>
                                        </template>
                                    </Column>

                                    <Column header="Posição" :sortable="false">
                                        <template #body="slotProps">
                                            <div class="flex align-items-center justify-content-center">
                                                <div 
                                                    class="flex align-items-center justify-content-center text-white border-round font-bold"
                                                    :class="getPositionClass(slotProps.index + 1)"
                                                    style="width: 30px; height: 30px;"
                                                >
                                                    {{ slotProps.index + 1 }}
                                                </div>
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Distribuição de Votos -->
                    <div class="grid" v-if="votingResults && votingResults.length > 0">
                        <div class="col-12 md:col-6">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Distribuição Votos Público</h6>
                                <Chart type="doughnut" :data="publicVotesChartData" :options="chartOptions" />
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="surface-card p-4 border-round shadow-2">
                                <h6 class="text-primary mb-3">Distribuição Votos Júri</h6>
                                <Chart type="doughnut" :data="judgeVotesChartData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center p-4">
                    <Message severity="error" class="w-full">
                        Episódio não encontrado ou erro ao carregar estatísticas
                    </Message>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

// Filter match mode constant
const FilterMatchMode = {
    CONTAINS: 'contains'
}

const route = useRoute()
const toast = useToast()

// Data
const loading = ref(true)
const episode = ref(null)
const stats = ref({})
const votingResults = ref([])
const votingTimeline = ref([])
const participantStats = ref([])
const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
})

// Chart options
const chartOptions = ref({
    plugins: {
        legend: {
            position: 'bottom'
        }
    },
    responsive: true,
    maintainAspectRatio: false
})

const timelineChartOptions = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true
        }
    }
})

// Computed
const publicVotesChartData = computed(() => {
    if (!votingResults.value || votingResults.value.length === 0) return {}
    
    return {
        labels: votingResults.value.map(r => r.participant_name),
        datasets: [{
            data: votingResults.value.map(r => r.public_votes),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ]
        }]
    }
})

const judgeVotesChartData = computed(() => {
    if (!votingResults.value || votingResults.value.length === 0) return {}
    
    return {
        labels: votingResults.value.map(r => r.participant_name),
        datasets: [{
            data: votingResults.value.map(r => r.judge_average),
            backgroundColor: [
                '#FFD700',
                '#FFA500',
                '#FF8C00',
                '#FF7F50',
                '#FF6347',
                '#FF4500',
                '#DC143C',
                '#B22222'
            ]
        }]
    }
})

const timelineChartData = computed(() => {
    if (!votingTimeline.value || votingTimeline.value.length === 0) return {}
    
    return {
        labels: votingTimeline.value.map(t => `${t.hour}:00`),
        datasets: [{
            label: 'Votos por Hora',
            data: votingTimeline.value.map(t => t.votes),
            borderColor: '#42A5F5',
            backgroundColor: 'rgba(66, 165, 245, 0.1)',
            tension: 0.4,
            fill: true
        }]
    }
})

// Methods
const loadStats = async () => {
    loading.value = true
    try {
        const response = await axios.get(`/api/web/episodes/${route.params.id}/stats`)
        if (response.data.success) {
            const data = response.data.data
            episode.value = data.episode
            stats.value = data.statistics
            votingResults.value = data.voting_results || []
            votingTimeline.value = data.voting_timeline || []
            
            // Preparar dados para a tabela de análise
            participantStats.value = votingResults.value.map((result, index) => ({
                ...result,
                position: index + 1
            }))
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar estatísticas',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

const clearFilter = () => {
    filters.value['global'].value = null
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

const getPositionClass = (position) => {
    if (position === 1) return 'bg-yellow-500'
    if (position === 2) return 'bg-gray-400'
    if (position === 3) return 'bg-orange-600'
    return 'bg-primary'
}

// Lifecycle
onMounted(() => {
    loadStats()
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

:deep(.p-chart) {
    height: 300px;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 1rem;
}

:deep(.p-progressbar) {
    height: 0.5rem;
}
</style>
