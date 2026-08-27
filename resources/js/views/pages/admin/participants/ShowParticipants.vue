<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useRouter, useRoute } from 'vue-router';
import moment from 'moment';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const isLoading = ref(true);
const participant = ref(null);
const votingStats = ref(null);

const fetchParticipant = async () => {
    try {
        const response = await axios.get(`/api/web/participants/${route.params.id}`);
        participant.value = response.data.data;
        
        // Get voting stats
        const statsResponse = await axios.get(`/api/web/participants/${route.params.id}/stats`);
        votingStats.value = statsResponse.data.data;
        
        isLoading.value = false;
    } catch (error) {
        toast.add({ 
            severity: 'error', 
            summary: 'Erro', 
            detail: 'Erro ao carregar participante', 
            life: 3000 
        });
        router.push('/admin/participants');
    }
};

const getSeverity = (active) => {
    return active ? 'success' : 'danger';
};

const getStatusText = (active) => {
    return active ? 'Ativo' : 'Inativo';
};

const getGenderText = (gender) => {
    const genderMap = {
        'M': 'Masculino',
        'F': 'Feminino',
        'Other': 'Outro'
    };
    return genderMap[gender] || gender;
};

const goBack = () => {
    router.push('/admin/participants');
};

const editParticipant = () => {
    router.push(`/admin/participants/${route.params.id}/edit`);
};

const toggleStatus = () => {
    axios.post(`/api/web/participants/${participant.value.id}/toggle-status`)
        .then((response) => {
            participant.value.active = response.data.data.active;
            toast.add({ 
                severity: 'success', 
                summary: 'Sucesso', 
                detail: response.data.message, 
                life: 3000 
            });
        })
        .catch((error) => {
            toast.add({ 
                severity: 'error', 
                summary: 'Erro', 
                detail: error.response?.data?.message || 'Erro ao alterar status', 
                life: 3000 
            });
        });
};

onMounted(() => {
    fetchParticipant();
});
</script>

<template>
    <div class="flex flex-col md:flex-row gap-12 min-h-screen items-center justify-center" v-if="isLoading">
        <div class="w-full">
            <div class="flex flex-col gap-4 text-center">
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />
                <p>Carregando participante...</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-12" v-else-if="participant">
        <div class="w-full">
            <div class="card flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">{{ participant.name }}</h1>
                        <p v-if="participant.stage_name" class="text-lg text-gray-600">"{{ participant.stage_name }}"</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <Button 
                            icon="pi pi-arrow-left" 
                            label="Voltar" 
                            severity="secondary" 
                            outlined 
                            @click="goBack"
                        />
                        <Button 
                            icon="pi pi-pencil" 
                            label="Editar" 
                            @click="editParticipant"
                        />
                        <Button 
                            :icon="participant.active ? 'pi pi-eye-slash' : 'pi pi-eye'"
                            :label="participant.active ? 'Desativar' : 'Ativar'"
                            :severity="participant.active ? 'warning' : 'success'"
                            outlined
                            @click="toggleStatus"
                        />
                    </div>
                </div>

                <Divider />

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Photo and Basic Info -->
                    <div class="lg:col-span-1">
                        <div class="card">
                            <div class="text-center">
                                <img 
                                    :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                                    :alt="participant.name"
                                    class="w-48 h-48 mx-auto rounded-lg object-cover border-4 border-gray-200 mb-4"
                                />
                                
                                <div class="space-y-2">
                                    <div class="text-lg font-semibold">{{ participant.name }}</div>
                                    <div v-if="participant.stage_name" class="text-gray-600">"{{ participant.stage_name }}"</div>
                                    
                                    <div class="flex justify-center">
                                        <Tag :value="getStatusText(participant.active)" :severity="getSeverity(participant.active)" />
                                    </div>
                                    
                                    <div v-if="participant.eliminated_episode" class="text-center">
                                        <Tag value="ELIMINADO" severity="danger" />
                                        <div class="text-sm text-gray-500 mt-1">
                                            Episódio {{ participant.eliminated_episode.episode_number }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Voting Code -->
                        <div class="card mt-4">
                            <div class="text-center">
                                <h3 class="text-lg font-semibold mb-2">Código de Votação</h3>
                                <div class="bg-blue-100 text-blue-800 text-2xl font-bold py-4 px-6 rounded-lg">
                                    {{ participant.voting_code }}
                                </div>
                                <small class="text-gray-500">Para votação SMS/WhatsApp</small>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4">Informações Pessoais</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Idade</label>
                                    <div class="text-lg">{{ participant.age || 'Não informado' }} anos</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Sexo</label>
                                    <div class="text-lg">{{ getGenderText(participant.gender) || 'Não informado' }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Cidade</label>
                                    <div class="text-lg">{{ participant.city || 'Não informado' }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Província</label>
                                    <div class="text-lg">{{ participant.province || 'Não informado' }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Ocupação</label>
                                    <div class="text-lg">{{ participant.occupation || 'Não informado' }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Data de Cadastro</label>
                                    <div class="text-lg">{{ moment(participant.created_at).format('DD/MM/YYYY HH:mm') }}</div>
                                </div>
                            </div>
                            
                            <!-- Biography -->
                            <div v-if="participant.biography" class="mt-6">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Biografia</label>
                                <div class="text-lg leading-relaxed">{{ participant.biography }}</div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="card mt-4" v-if="participant.instagram || participant.facebook || participant.tiktok">
                            <h3 class="text-lg font-semibold mb-4">Redes Sociais</h3>
                            
                            <div class="flex flex-wrap gap-4">
                                <div v-if="participant.instagram" class="flex items-center gap-2">
                                    <i class="pi pi-instagram text-pink-500"></i>
                                    <a :href="`https://instagram.com/${participant.instagram.replace('@', '')}`" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        {{ participant.instagram }}
                                    </a>
                                </div>
                                
                                <div v-if="participant.facebook" class="flex items-center gap-2">
                                    <i class="pi pi-facebook text-blue-600"></i>
                                    <a :href="`https://facebook.com/${participant.facebook}`" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        {{ participant.facebook }}
                                    </a>
                                </div>
                                
                                <div v-if="participant.tiktok" class="flex items-center gap-2">
                                    <i class="pi pi-video text-black"></i>
                                    <a :href="`https://tiktok.com/@${participant.tiktok.replace('@', '')}`" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        {{ participant.tiktok }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Voting Statistics -->
                        <div class="card mt-4" v-if="votingStats">
                            <h3 class="text-lg font-semibold mb-4">Estatísticas de Votação</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ votingStats.total_votes || 0 }}</div>
                                    <div class="text-sm text-gray-500">Total de Votos</div>
                                </div>
                                
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ votingStats.current_episode_votes || 0 }}</div>
                                    <div class="text-sm text-gray-500">Votos no Episódio Atual</div>
                                </div>
                                
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ votingStats.average_votes_per_episode || 0 }}</div>
                                    <div class="text-sm text-gray-500">Média por Episódio</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    .md\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .lg\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    
    .lg\:col-span-1 {
        grid-column: span 1 / span 1;
    }
    
    .lg\:col-span-2 {
        grid-column: span 2 / span 2;
    }
}

.gap-4 {
    gap: 1rem;
}

.gap-6 {
    gap: 1.5rem;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}
</style>
