<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const router = useRouter();
const toast = useToast();

// Reactive data
const isLoadingDiv = ref(true);
const participants = ref([]);
const currentEpisode = ref(null);
const selectedParticipant = ref(null);
const voteValue = ref(null);
const submittingVote = ref(false);
const displayVoteDialog = ref(false);
const displayConfirmation = ref(false);

// Computed properties
const votingStatus = computed(() => {
    if (!currentEpisode.value) return { active: false, message: 'Nenhum episódio ativo' };
    
    if (!currentEpisode.value.voting_open) {
        return { active: false, message: 'Votação fechada para este episódio' };
    }
    
    const now = new Date();
    const votingStart = new Date(currentEpisode.value.voting_start);
    const votingEnd = new Date(currentEpisode.value.voting_end);
    
    if (now < votingStart) {
        return { active: false, message: `Votação inicia em ${votingStart.toLocaleDateString()}` };
    }
    
    if (now > votingEnd) {
        return { active: false, message: 'Período de votação encerrado' };
    }
    
    return { active: true, message: 'Votação ativa' };
});

// Methods
const fetchCurrentEpisode = async () => {
    try {
        const response = await axios.get('/api/votaaqui/episodes/current');
        currentEpisode.value = response.data.data;
    } catch (error) {
        console.error('Erro ao buscar episódio atual:', error);
        toast.add({ 
            severity: 'warn', 
            summary: 'Aviso', 
            detail: 'Nenhum episódio ativo encontrado', 
            life: 3000 
        });
    }
};

const fetchParticipants = async () => {
    try {
        const response = await axios.get('/api/participants');
        participants.value = response.data.data || response.data;
    } catch (error) {
        console.error('Erro ao carregar participantes:', error);
        toast.add({ 
            severity: 'error', 
            summary: 'Erro', 
            detail: 'Erro ao carregar participantes', 
            life: 3000 
        });
    }
};

const openVoteDialog = (participant) => {
    if (!votingStatus.value.active) {
        toast.add({ 
            severity: 'warn', 
            summary: 'Votação Indisponível', 
            detail: votingStatus.value.message, 
            life: 3000 
        });
        return;
    }
    
    selectedParticipant.value = participant;
    voteValue.value = null;
    displayVoteDialog.value = true;
};

const submitVote = async () => {
    if (!voteValue.value || voteValue.value < 1 || voteValue.value > 10) {
        toast.add({ 
            severity: 'warn', 
            summary: 'Voto Inválido', 
            detail: 'O voto deve ser entre 1 e 10', 
            life: 3000 
        });
        return;
    }
    
    submittingVote.value = true;
    
    try {
        const response = await axios.post('/api/web/judgevotes', {
            participant_id: selectedParticipant.value.id,
            episode_id: currentEpisode.value.id,
            final_score: voteValue.value
        });
        
        toast.add({ 
            severity: 'success', 
            summary: 'Voto Registrado', 
            detail: `Voto ${voteValue.value} para ${selectedParticipant.value.stage_name} registrado com sucesso!`, 
            life: 4000 
        });
        
        displayVoteDialog.value = false;
        
        // Atualizar a lista de participantes para mostrar que já votou
        await fetchParticipants();
        
    } catch (error) {
        console.error('Erro ao submeter voto:', error);
        const errorMessage = error.response?.data?.message || 'Erro ao registrar voto';
        toast.add({ 
            severity: 'error', 
            summary: 'Erro', 
            detail: errorMessage, 
            life: 3000 
        });
    } finally {
        submittingVote.value = false;
    }
};

const closeVoteDialog = () => {
    displayVoteDialog.value = false;
    selectedParticipant.value = null;
    voteValue.value = null;
};

const closeConfirmation = () => {
    displayConfirmation.value = false;
};

const handleImageError = (event) => {
    event.target.src = '/votaaqui/assets/img/events/gate1.jpeg';
};

const initializeData = async () => {
    isLoadingDiv.value = true;
    
    try {
        await Promise.all([
            fetchCurrentEpisode(),
            fetchParticipants()
        ]);
    } catch (error) {
        console.error('Erro ao inicializar dados:', error);
    } finally {
        isLoadingDiv.value = false;
    }
};

onMounted(() => {
    initializeData();
});

</script>

<style scoped>
.jury-voting-container {
    min-height: 100vh;
    /* background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%); */
    padding: 2rem;
}

.voting-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
    color: white;
}

.header-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title i {
    color: #fbbf24;
    font-size: 2rem;
    text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}

.episode-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.episode-badge {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.episode-number {
    font-size: 1.2rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: inline-block;
}

.episode-title {
    font-size: 1rem;
    opacity: 0.9;
}

.voting-status {
    display: flex;
    align-items: center;
}

.status-alert {
    margin-bottom: 2rem;
}

.participants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.participant-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.participant-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.participant-card.voted {
    border-color: #dc2626;
    background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.card-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.participant-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.participant-card:hover .participant-image {
    transform: scale(1.05);
}

.voted-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
}

.card-content {
    padding: 1.5rem;
}

.participant-info {
    margin-bottom: 1.5rem;
}

.participant-name {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #2d3748;
}

.participant-bio {
    color: #718096;
    margin: 0;
    line-height: 1.5;
}

.vote-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.current-score {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fef2f2;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #dc2626;
}

.score-label {
    font-weight: 600;
    color: #4a5568;
}

.score-display {
    font-size: 1.5rem;
    font-weight: 700;
    color: #dc2626;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 2px solid #dc2626;
}

.vote-button {
    width: 100%;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.vote-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #718096;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

/* Vote Dialog Styles */
.vote-dialog-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.participant-preview {
    display: flex;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-radius: 12px;
    border: 1px solid rgba(220, 38, 38, 0.1);
}

.preview-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #dc2626;
}

.preview-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1.2rem;
    color: #2d3748;
}

.preview-info p {
    margin: 0;
    color: #718096;
    font-size: 0.9rem;
}

.vote-input-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.vote-label {
    font-weight: 600;
    color: #dc2626;
    color: #4a5568;
    display: flex;
    align-items: center;
}

.score-input-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.score-input {
    width: 100%;
}

.score-indicators {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
}

.score-btn {
    aspect-ratio: 1;
    border-radius: 8px;
    font-weight: 600;
    border: 2px solid #fee2e2;
    background: white;
    color: #dc2626;
    transition: all 0.3s ease;
}

.score-btn:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    border-color: #dc2626;
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 768px) {
    .jury-voting-container {
        padding: 1rem;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .participants-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .episode-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .score-indicators {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width: 480px) {
    .header-title {
        font-size: 1.5rem;
        flex-direction: column;
        text-align: center;
    }
    
    .participant-preview {
        flex-direction: column;
        text-align: center;
    }
    
    .score-indicators {
        grid-template-columns: repeat(5, 1fr);
    }
}

/* Animation Enhancements */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.participant-card {
    animation: slideInUp 0.6s ease forwards;
}

.participant-card:nth-child(1) { animation-delay: 0.1s; }
.participant-card:nth-child(2) { animation-delay: 0.2s; }
.participant-card:nth-child(3) { animation-delay: 0.3s; }
.participant-card:nth-child(4) { animation-delay: 0.4s; }
.participant-card:nth-child(5) { animation-delay: 0.5s; }
.participant-card:nth-child(6) { animation-delay: 0.6s; }

/* PrimeVue Badge Custom Styles */
:deep(.p-badge.p-badge-success) {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

:deep(.p-badge.p-badge-warn) {
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(234, 88, 12, 0.3);
}

/* Red themed button styles */
:deep(.p-button.p-button-success) {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: 1px solid #dc2626;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

:deep(.p-button.p-button-success:hover) {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    border-color: #b91c1c;
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
}

:deep(.p-button.p-button-secondary) {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border: 1px solid #ef4444;
    color: white;
}

:deep(.p-button.p-button-secondary:hover) {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border-color: #dc2626;
}
</style>

<template>
    <!-- Loading State -->
    <div class="flex flex-col md:flex-row gap-12 min-h-screen items-center justify-center" v-if="isLoadingDiv">
        <div class="w-full">
            <div class="flex flex-col gap-4 text-center">
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />
                <p>Por Favor Aguarde...</p>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div v-else class="jury-voting-container">
        <!-- Header Section -->
        <div class="voting-header">
            <div class="header-content">
                <h1 class="header-title">
                    <i class="pi pi-star-fill"></i>
                    Painel de Votação do Júri
                </h1>
                <div class="episode-info" v-if="currentEpisode">
                    <div class="episode-badge">
                        <span class="episode-number">Episódio {{ currentEpisode.episode_number }}</span>
                        <span class="episode-title">{{ currentEpisode.title }}</span>
                    </div>
                    <div class="voting-status">
                        <Badge 
                            :value="votingStatus.message" 
                            :severity="votingStatus.active ? 'success' : 'warn'"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Voting Status Alert -->
        <div v-if="!votingStatus.active" class="status-alert">
            <Message 
                :severity="'warn'" 
                :closable="false"
            >
                <i class="pi pi-exclamation-triangle mr-2"></i>
                {{ votingStatus.message }}
            </Message>
        </div>

        <!-- Participants Grid -->
        <div class="participants-grid">
            <div 
                v-for="participant in participants" 
                :key="participant.id"
                class="participant-card"
                :class="{ 'voted': participant.has_judge_vote }"
            >
                <div class="card-image-container">
                    <img 
                        :src="participant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'"
                        :alt="participant.name"
                        class="participant-image"
                        @error="handleImageError"
                    />
                    <div v-if="participant.has_judge_vote" class="voted-overlay">
                        <i class="pi pi-check-circle"></i>
                        <span>Votado</span>
                    </div>
                </div>
                
                <div class="card-content">
                    <div class="participant-info">
                        <h3 class="participant-name">{{ participant.stage_name || participant.name }}</h3>
                        <p class="participant-bio">{{ participant.biography || 'Participante do Reality Show' }}</p>
                    </div>
                    
                    <div class="vote-section">
                        <div v-if="participant.judge_score" class="current-score">
                            <span class="score-label">Sua nota:</span>
                            <div class="score-display">
                                {{ participant.judge_score }}/10
                            </div>
                        </div>
                        
                        <Button 
                            @click="openVoteDialog(participant)"
                            :label="participant.has_judge_vote ? 'Alterar Voto' : 'Votar'"
                            :icon="participant.has_judge_vote ? 'pi pi-pencil' : 'pi pi-star'"
                            class="vote-button"
                            :class="{ 
                                'p-button-success': !participant.has_judge_vote && votingStatus.active,
                                'p-button-secondary': participant.has_judge_vote,
                                'p-button-help': !votingStatus.active
                            }"
                            :disabled="!votingStatus.active"
                        />
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-if="participants.length === 0" class="empty-state">
                <i class="pi pi-users"></i>
                <h3>Nenhum participante encontrado</h3>
                <p>Não há participantes disponíveis para votação neste momento.</p>
            </div>
        </div>
    </div>

    <!-- Vote Dialog -->
    <Dialog 
        v-model:visible="displayVoteDialog" 
        :modal="true" 
        header="Registrar Voto" 
        class="vote-dialog"
        :style="{ width: '400px' }"
        :closable="!submittingVote"
    >
        <div v-if="selectedParticipant" class="vote-dialog-content">
            <div class="participant-preview">
                <img 
                    :src="selectedParticipant.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'"
                    :alt="selectedParticipant.name"
                    class="preview-image"
                />
                <div class="preview-info">
                    <h4>{{ selectedParticipant.stage_name || selectedParticipant.name }}</h4>
                    <p>{{ selectedParticipant.biography || 'Participante do Reality Show' }}</p>
                </div>
            </div>
            
            <div class="vote-input-section">
                <label for="voteValue" class="vote-label">
                    <i class="pi pi-star mr-2"></i>
                    Sua nota (1 a 10):
                </label>
                <div class="score-input-container">
                    <InputNumber 
                        id="voteValue"
                        v-model="voteValue" 
                        :min="1" 
                        :max="10"
                        :step="1"
                        placeholder="Digite sua nota"
                        class="score-input"
                        :disabled="submittingVote"
                    />
                    <div class="score-indicators">
                        <Button 
                            v-for="score in [1,2,3,4,5,6,7,8,9,10]" 
                            :key="score"
                            :label="score.toString()"
                            @click="voteValue = score"
                            class="score-btn"
                            :class="{ 'p-button-outlined': voteValue !== score }"
                            size="small"
                            :disabled="submittingVote"
                        />
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button 
                label="Cancelar" 
                icon="pi pi-times" 
                @click="closeVoteDialog" 
                class="p-button-text"
                :disabled="submittingVote" 
            />
            <Button 
                :label="submittingVote ? 'Registrando...' : 'Confirmar Voto'" 
                :icon="submittingVote ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
                @click="submitVote" 
                :disabled="!voteValue || submittingVote"
                :loading="submittingVote"
            />
        </template>
    </Dialog>

    <!-- Confirmation Dialog -->
    <Dialog header="Confirmação" v-model:visible="displayConfirmation" :style="{ width: '350px' }" :modal="true">
        <div class="flex align-items-center justify-content-center">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span>Tem certeza que deseja proceder?</span>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" @click="closeConfirmation" class="p-button-text" />
            <Button label="Sim" icon="pi pi-check" @click="closeConfirmation" class="p-button-text" autofocus />
        </template>
    </Dialog>
</template>