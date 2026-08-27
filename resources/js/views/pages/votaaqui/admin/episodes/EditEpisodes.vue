<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <div class="flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0">Editar Episódio</h5>
                    <Button 
                        label="Voltar" 
                        icon="pi pi-arrow-left" 
                        class="p-button-outlined"
                        @click="$router.push('/admin/episodes')"
                    />
                </div>

                <div v-if="loadingData" class="text-center p-4">
                    <ProgressSpinner />
                    <p>Carregando dados do episódio...</p>
                </div>

                <form v-else @submit.prevent="submitForm">
                    <div class="grid">
                        <!-- Informações Básicas -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Informações Básicas</h6>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="episode_number" class="block text-900 font-medium mb-2">
                                    Número do Episódio <span class="text-red-500">*</span>
                                </label>
                                <InputNumber
                                    id="episode_number"
                                    v-model="form.episode_number"
                                    :min="1"
                                    :max="999"
                                    showButtons
                                    :class="{ 'p-invalid': errors.episode_number }"
                                    class="w-full"
                                />
                                <small v-if="errors.episode_number" class="p-error">{{ errors.episode_number }}</small>
                            </div>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="episode_type" class="block text-900 font-medium mb-2">
                                    Tipo de Episódio <span class="text-red-500">*</span>
                                </label>
                                <Dropdown
                                    id="episode_type"
                                    v-model="form.episode_type"
                                    :options="episodeTypes"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Selecione o tipo"
                                    :class="{ 'p-invalid': errors.episode_type }"
                                    class="w-full"
                                    :disabled="hasVotes"
                                />
                                <small v-if="errors.episode_type" class="p-error">{{ errors.episode_type }}</small>
                                <small v-if="hasVotes" class="text-muted">
                                    <i class="pi pi-info-circle"></i> 
                                    Não é possível alterar o tipo de episódio que já possui votos
                                </small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="field">
                                <label for="title" class="block text-900 font-medium mb-2">
                                    Título <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    id="title"
                                    v-model="form.title"
                                    :class="{ 'p-invalid': errors.title }"
                                    class="w-full"
                                    placeholder="Ex: Primeira Eliminação"
                                />
                                <small v-if="errors.title" class="p-error">{{ errors.title }}</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="field">
                                <label for="description" class="block text-900 font-medium mb-2">Descrição</label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    :class="{ 'p-invalid': errors.description }"
                                    class="w-full"
                                    placeholder="Descrição do episódio..."
                                />
                                <small v-if="errors.description" class="p-error">{{ errors.description }}</small>
                            </div>
                        </div>

                        <!-- Configurações de Data e Status -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Datas e Status</h6>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="air_date" class="block text-900 font-medium mb-2">
                                    Data de Transmissão <span class="text-red-500">*</span>
                                </label>
                                <Calendar
                                    id="air_date"
                                    v-model="form.air_date"
                                    showTime
                                    hourFormat="24"
                                    :class="{ 'p-invalid': errors.air_date }"
                                    class="w-full"
                                    placeholder="dd/mm/aaaa hh:mm"
                                    dateFormat="dd/mm/yy"
                                />
                                <small v-if="errors.air_date" class="p-error">{{ errors.air_date }}</small>
                            </div>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="status" class="block text-900 font-medium mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <Dropdown
                                    id="status"
                                    v-model="form.status"
                                    :options="statusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Selecione o status"
                                    :class="{ 'p-invalid': errors.status }"
                                    class="w-full"
                                />
                                <small v-if="errors.status" class="p-error">{{ errors.status }}</small>
                            </div>
                        </div>

                        <!-- Configurações de Votação -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Configurações de Votação</h6>
                        </div>

                        <div class="col-12">
                            <div class="field-checkbox mb-3">
                                <Checkbox 
                                    id="voting_open" 
                                    v-model="form.voting_open" 
                                    :binary="true"
                                />
                                <label for="voting_open" class="ml-2">Votação Aberta</label>
                            </div>
                        </div>

                        <div v-if="form.voting_open" class="col-12">
                            <div class="grid">
                                <div class="col-12 md:col-6">
                                    <div class="field">
                                        <label for="voting_start" class="block text-900 font-medium mb-2">
                                            Início da Votação <span class="text-red-500">*</span>
                                        </label>
                                        <Calendar
                                            id="voting_start"
                                            v-model="form.voting_start"
                                            showTime
                                            hourFormat="24"
                                            :class="{ 'p-invalid': errors.voting_start }"
                                            class="w-full"
                                            placeholder="dd/mm/aaaa hh:mm"
                                            dateFormat="dd/mm/yy"
                                            :disabled="hasVotes"
                                        />
                                        <small v-if="errors.voting_start" class="p-error">{{ errors.voting_start }}</small>
                                        <small v-if="hasVotes" class="text-muted">
                                            <i class="pi pi-info-circle"></i> 
                                            Não é possível alterar datas de votação que já possui votos
                                        </small>
                                    </div>
                                </div>

                                <div class="col-12 md:col-6">
                                    <div class="field">
                                        <label for="voting_end" class="block text-900 font-medium mb-2">
                                            Fim da Votação <span class="text-red-500">*</span>
                                        </label>
                                        <Calendar
                                            id="voting_end"
                                            v-model="form.voting_end"
                                            showTime
                                            hourFormat="24"
                                            :class="{ 'p-invalid': errors.voting_end }"
                                            class="w-full"
                                            placeholder="dd/mm/aaaa hh:mm"
                                            dateFormat="dd/mm/yy"
                                            :disabled="hasVotes"
                                        />
                                        <small v-if="errors.voting_end" class="p-error">{{ errors.voting_end }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pesos da Votação -->
                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="jury_weight" class="block text-900 font-medium mb-2">
                                    Peso do Júri (%) <span class="text-red-500">*</span>
                                </label>
                                <InputNumber
                                    id="jury_weight"
                                    v-model="form.jury_weight"
                                    :min="0"
                                    :max="100"
                                    suffix="%"
                                    :class="{ 'p-invalid': errors.jury_weight }"
                                    class="w-full"
                                    :disabled="hasVotes"
                                />
                                <small v-if="errors.jury_weight" class="p-error">{{ errors.jury_weight }}</small>
                            </div>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="field">
                                <label for="public_weight" class="block text-900 font-medium mb-2">
                                    Peso Público (%) <span class="text-red-500">*</span>
                                </label>
                                <InputNumber
                                    id="public_weight"
                                    v-model="form.public_weight"
                                    :min="0"
                                    :max="100"
                                    suffix="%"
                                    :class="{ 'p-invalid': errors.public_weight }"
                                    class="w-full"
                                    :disabled="hasVotes"
                                />
                                <small v-if="errors.public_weight" class="p-error">{{ errors.public_weight }}</small>
                                <small v-if="hasVotes" class="text-muted">
                                    <i class="pi pi-info-circle"></i> 
                                    Não é possível alterar pesos de votação que já possui votos
                                </small>
                            </div>
                        </div>

                        <!-- Participantes -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Participantes</h6>
                        </div>

                        <div class="col-12">
                            <div class="field">
                                <label class="block text-900 font-medium mb-2">Selecionar Participantes</label>
                                <MultiSelect
                                    v-model="form.participant_ids"
                                    :options="availableParticipants"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Selecione os participantes"
                                    :filter="true"
                                    class="w-full"
                                    display="chip"
                                />
                                <small class="text-muted">
                                    Participantes que irão participar deste episódio
                                </small>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="col-12">
                            <div class="field">
                                <label for="notes" class="block text-900 font-medium mb-2">Notas</label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    :class="{ 'p-invalid': errors.notes }"
                                    class="w-full"
                                    placeholder="Observações adicionais..."
                                />
                                <small v-if="errors.notes" class="p-error">{{ errors.notes }}</small>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="col-12">
                            <div class="flex gap-2 justify-content-end">
                                <Button 
                                    label="Cancelar" 
                                    icon="pi pi-times" 
                                    class="p-button-outlined"
                                    @click="$router.push('/admin/episodes')"
                                    type="button"
                                />
                                <Button 
                                    label="Salvar Alterações" 
                                    icon="pi pi-check" 
                                    type="submit"
                                    :loading="loading"
                                />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const toast = useToast()

// Data
const loading = ref(false)
const loadingData = ref(true)
const availableParticipants = ref([])
const errors = ref({})
const episode = ref(null)

const form = ref({
    episode_number: null,
    title: '',
    description: '',
    episode_type: 'elimination',
    air_date: null,
    status: 'scheduled',
    voting_open: false,
    voting_start: null,
    voting_end: null,
    jury_weight: 50,
    public_weight: 50,
    participant_ids: [],
    notes: ''
})

const episodeTypes = [
    { label: 'Eliminação', value: 'elimination' },
    { label: 'Final', value: 'finale' },
    { label: 'Especial', value: 'special' },
    { label: 'Audição', value: 'audition' }
]

const statusOptions = [
    { label: 'Agendado', value: 'scheduled' },
    { label: 'Ao Vivo', value: 'live' },
    { label: 'Finalizado', value: 'finished' }
]

// Computed
const hasVotes = computed(() => {
    return episode.value && (episode.value.public_votes_count > 0 || episode.value.judge_votes_count > 0)
})

// Methods
const loadEpisode = async () => {
    try {
        const response = await axios.get(`/api/web/episodes/${route.params.id}`)
        if (response.data.success) {
            episode.value = response.data.data
            
            // Populate form
            form.value = {
                episode_number: episode.value.episode_number,
                title: episode.value.title,
                description: episode.value.description || '',
                episode_type: episode.value.episode_type,
                air_date: new Date(episode.value.air_date),
                status: episode.value.status,
                voting_open: episode.value.voting_open,
                voting_start: episode.value.voting_start ? new Date(episode.value.voting_start) : null,
                voting_end: episode.value.voting_end ? new Date(episode.value.voting_end) : null,
                jury_weight: episode.value.jury_weight,
                public_weight: episode.value.public_weight,
                participant_ids: episode.value.participants?.map(p => p.id) || [],
                notes: episode.value.notes || ''
            }
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar episódio',
            life: 3000
        })
        router.push('/admin/episodes')
    } finally {
        loadingData.value = false
    }
}

const loadAvailableParticipants = async () => {
    try {
        const response = await axios.get('/api/web/episodes/participants/available')
        if (response.data.success) {
            availableParticipants.value = response.data.data
        }
    } catch (error) {
        console.error('Erro ao carregar participantes:', error)
    }
}

const updatePublicWeight = () => {
    if (form.value.jury_weight !== null) {
        form.value.public_weight = 100 - form.value.jury_weight
    }
}

const updateJuryWeight = () => {
    if (form.value.public_weight !== null) {
        form.value.jury_weight = 100 - form.value.public_weight
    }
}

const submitForm = async () => {
    loading.value = true
    errors.value = {}

    try {
        const response = await axios.put(`/api/web/episodes/${route.params.id}`, form.value)
        
        if (response.data.success) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: response.data.message,
                life: 3000
            })
            
            router.push('/admin/episodes')
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {}
        }
        
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: error.response?.data?.message || 'Erro ao atualizar episódio',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

// Lifecycle
onMounted(async () => {
    await Promise.all([
        loadEpisode(),
        loadAvailableParticipants()
    ])
})
</script>

<style scoped>
.card {
    box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}

.field {
    margin-bottom: 1rem;
}

.text-red-500 {
    color: #ef4444;
}

.text-primary {
    color: var(--primary-color);
}

.text-muted {
    color: #6b7280;
}
</style>
