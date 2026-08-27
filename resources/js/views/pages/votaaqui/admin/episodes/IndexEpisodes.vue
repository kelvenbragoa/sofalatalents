
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
import { debounce } from 'lodash-es';

const router = useRouter()
const toast = useToast()

// Data
const episodes = ref([])
const loading = ref(false)
const deleteLoading = ref(false)
const searchQuery = ref('')
const statusFilter = ref(null)
const deleteDialog = ref(false)
const episodeToDelete = ref(null)

const pagination = ref({
    page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
})

const statusOptions = [
    { label: 'Agendado', value: 'scheduled' },
    { label: 'Ao Vivo', value: 'live' },
    { label: 'Finalizado', value: 'finished' }
]

// Methods
const loadEpisodes = async (page = 1) => {
    loading.value = true
    try {
        const params = {
            page,
            query: searchQuery.value,
            status: statusFilter.value
        }

        const response = await axios.get('/api/web/episodes', { params })
        episodes.value = response.data.data
        pagination.value = {
            page: response.data.current_page,
            per_page: response.data.per_page,
            total: response.data.total,
            last_page: response.data.last_page
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar episódios',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

const debouncedSearch = debounce(() => {
    loadEpisodes(1)
}, 500)

const onPage = (event) => {
    loadEpisodes(event.page + 1)
}

const refreshData = () => {
    searchQuery.value = ''
    statusFilter.value = null
    loadEpisodes(1)
}

const viewEpisode = (id) => {
    router.push(`/admin/episodes/${id}`)
}

const editEpisode = (id) => {
    router.push(`/admin/episodes/${id}/edit`)
}

const viewStats = (id) => {
    router.push(`/admin/episodes/${id}/stats`)
}

const toggleVoting = async (episode) => {
    try {
        const response = await axios.post(`/api/web/episodes/${episode.id}/toggle-voting`)
        
        if (response.data.success) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: response.data.message,
                life: 3000
            })
            
            // Update episode in list
            const index = episodes.value.findIndex(e => e.id === episode.id)
            if (index !== -1) {
                episodes.value[index].voting_open = response.data.data.voting_open
            }
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: error.response?.data?.message || 'Erro ao alterar status da votação',
            life: 3000
        })
    }
}

const confirmDelete = (episode) => {
    episodeToDelete.value = episode
    deleteDialog.value = true
}

const deleteEpisode = async () => {
    deleteLoading.value = true
    try {
        const response = await axios.delete(`/api/web/episodes/${episodeToDelete.value.id}`)
        
        if (response.data.success) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: response.data.message,
                life: 3000
            })
            
            deleteDialog.value = false
            episodeToDelete.value = null
            loadEpisodes(pagination.value.page)
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: error.response?.data?.message || 'Erro ao excluir episódio',
            life: 3000
        })
    } finally {
        deleteLoading.value = false
    }
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getStatusLabel = (status) => {
    const labels = {
        scheduled: 'Agendado',
        live: 'Ao Vivo',
        finished: 'Finalizado'
    }
    return labels[status] || status
}

const getStatusSeverity = (status) => {
    const severities = {
        scheduled: 'warning',
        live: 'success',
        finished: 'secondary'
    }
    return severities[status] || 'info'
}

// Lifecycle
onMounted(() => {
    loadEpisodes()
})
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <div class="font-semibold text-xl">Episódio</div>
                <div class="flex justify-content-between align-items-center mb-4">
                    
                    <Button 
                        label="Novo Episódio" 
                        icon="pi pi-plus" 
                        @click="$router.push('/admin/episodes/create')"
                        class="p-button-primary"
                    />
                </div>

                <div class="flex justify-content-between align-items-center mb-4 gap-2">
                    <div class="flex align-items-center gap-2">
                        <IconField iconPosition="left">
                            <InputIcon class="pi pi-search" />
                            <InputText 
                                v-model="searchQuery" 
                                placeholder="Buscar episódios..." 
                                @input="debouncedSearch"
                            />
                        </IconField>
                        <Dropdown 
                            v-model="statusFilter" 
                            :options="statusOptions" 
                            optionLabel="label" 
                            optionValue="value"
                            placeholder="Filtrar por status"
                            showClear
                            @change="loadEpisodes"
                        />
                    </div>
                    <Button 
                        icon="pi pi-refresh" 
                        @click="refreshData"
                        class="p-button-outlined"
                        v-tooltip="'Atualizar'"
                    />
                </div>

                <DataTable 
                    :value="episodes" 
                    :lazy="true"
                    :paginator="true" 
                    :rows="pagination.per_page"
                    :totalRecords="pagination.total"
                    :loading="loading"
                    @page="onPage"
                    dataKey="id"
                    responsiveLayout="scroll"
                    :rowHover="true"
                >
                    <Column field="episode_number" header="Nº" sortable style="min-width: 80px">
                        <template #body="{ data }">
                            <Tag 
                                :value="`#${data.episode_number}`" 
                                severity="info"
                            />
                        </template>
                    </Column>
                    
                    <Column field="title" header="Título" sortable style="min-width: 200px">
                        <template #body="{ data }">
                            <div>
                                <div class="font-medium">{{ data.title }}</div>
                                <small class="text-muted">{{ data.episode_type }}</small>
                            </div>
                        </template>
                    </Column>

                    <Column field="air_date" header="Data de Transmissão" sortable style="min-width: 150px">
                        <template #body="{ data }">
                            {{ formatDate(data.air_date) }}
                        </template>
                    </Column>

                    <Column field="status" header="Status" style="min-width: 120px">
                        <template #body="{ data }">
                            <Tag 
                                :value="getStatusLabel(data.status)" 
                                :severity="getStatusSeverity(data.status)"
                            />
                        </template>
                    </Column>

                    <Column field="voting_open" header="Votação" style="min-width: 100px">
                        <template #body="{ data }">
                            <Tag 
                                :value="data.voting_open ? 'Aberta' : 'Fechada'" 
                                :severity="data.voting_open ? 'success' : 'danger'"
                            />
                        </template>
                    </Column>

                    <Column field="participants_count" header="Participantes" style="min-width: 120px">
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <i class="pi pi-users text-primary"></i>
                                <span>{{ data.participants_count || 0 }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="public_votes_count" header="Votos" style="min-width: 100px">
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <i class="pi pi-heart text-red-500"></i>
                                <span>{{ data.public_votes_count || 0 }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Ações" style="min-width: 150px">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Button 
                                    icon="pi pi-eye" 
                                    class="p-button-rounded p-button-text p-button-sm"
                                    @click="viewEpisode(data.id)"
                                    v-tooltip="'Visualizar'"
                                />
                                <Button 
                                    icon="pi pi-pencil" 
                                    class="p-button-rounded p-button-text p-button-sm"
                                    @click="editEpisode(data.id)"
                                    v-tooltip="'Editar'"
                                />
                                <Button 
                                    :icon="data.voting_open ? 'pi pi-pause' : 'pi pi-play'" 
                                    :class="data.voting_open ? 'p-button-warning' : 'p-button-success'"
                                    class="p-button-rounded p-button-text p-button-sm"
                                    @click="toggleVoting(data)"
                                    v-tooltip="data.voting_open ? 'Fechar Votação' : 'Abrir Votação'"
                                />
                                <Button 
                                    icon="pi pi-chart-bar" 
                                    class="p-button-rounded p-button-text p-button-sm p-button-info"
                                    @click="viewStats(data.id)"
                                    v-tooltip="'Estatísticas'"
                                />
                                <Button 
                                    icon="pi pi-trash" 
                                    class="p-button-rounded p-button-text p-button-sm p-button-danger"
                                    @click="confirmDelete(data)"
                                    v-tooltip="'Excluir'"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog 
        v-model:visible="deleteDialog" 
        :style="{ width: '450px' }" 
        header="Confirmar Exclusão" 
        :modal="true"
    >
        <div class="flex align-items-center justify-content-center">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span v-if="episodeToDelete">
                Tem certeza que deseja excluir o episódio <b>{{ episodeToDelete.title }}</b>?
                <br><small class="text-muted">Esta ação não pode ser desfeita.</small>
            </span>
        </div>
        <template #footer>
            <Button 
                label="Cancelar" 
                icon="pi pi-times" 
                class="p-button-text" 
                @click="deleteDialog = false"
            />
            <Button 
                label="Excluir" 
                icon="pi pi-check" 
                class="p-button-danger" 
                @click="deleteEpisode" 
                :loading="deleteLoading"
            />
        </template>
    </Dialog>
</template>


<style scoped>
.card {
    box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}
</style>
