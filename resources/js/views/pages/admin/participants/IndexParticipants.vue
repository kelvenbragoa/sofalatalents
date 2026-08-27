<script setup>
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { onBeforeMount, reactive, ref, onMounted, watch } from 'vue';
import { RouterView, RouterLink, useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { debounce } from 'lodash-es';
import moment from 'moment';

const router = useRouter();
const toast = useToast();
const loading1 = ref(null);
const isLoadingDiv = ref(true);
const loadingButtonDelete = ref(false);
let dataIdBeingDeleted = ref(null);
const searchQuery = ref('');
const retriviedData = ref(null);
const currentPage = ref(1);
const rowsPerPage = ref(15);
const totalRecords = ref(0);
const displayConfirmation = ref(false);

function goBackUsingBack() {
    if (router) {
        router.back();
    }
}

const closeConfirmation = () => {
    displayConfirmation.value = false;
};

const confirmDeletion = (id) => {
    displayConfirmation.value = true;
    dataIdBeingDeleted.value = id;
};

function getSeverity(active) {
    return active ? 'success' : 'danger';
}

function getStatusText(active) {
    return active ? 'Ativo' : 'Inativo';
}

function getGenderText(gender) {
    const genderMap = {
        'M': 'Masculino',
        'F': 'Feminino',
        'Other': 'Outro'
    };
    return genderMap[gender] || gender;
}

const getData = async (page = 1) => {
    axios
        .get(`/api/web/participants?page=${page}`, {
            params: {
                query: searchQuery.value
            }
        })
        .then((response) => {
            retriviedData.value = response.data;
            totalRecords.value = response.data.total;
            isLoadingDiv.value = false;
        })
        .catch((error) => {
            isLoadingDiv.value = false;
            toast.add({ severity: 'error', summary: `${error}`, detail: 'Erro ao carregar dados', life: 3000 });
            goBackUsingBack();
        });
};

const deleteData = () => {
    loadingButtonDelete.value = true;

    axios
        .delete(`/api/web/participants/${dataIdBeingDeleted.value}`)
        .then(() => {
            retriviedData.value.data = retriviedData.value.data.filter((data) => data.id !== dataIdBeingDeleted.value);
            closeConfirmation();
            toast.add({ severity: 'success', summary: `Sucesso`, detail: 'Participante excluído com sucesso', life: 3000 });
        })
        .catch((error) => {
            toast.add({ severity: 'error', summary: `Erro`, detail: error.response?.data?.message || 'Erro ao excluir', life: 3000 });
            loadingButtonDelete.value = false;
        })
        .finally(() => {
            loadingButtonDelete.value = false;
        });
};

const toggleStatus = (participant) => {
    axios
        .post(`/api/web/participants/${participant.id}/toggle-status`)
        .then((response) => {
            // Update the participant in the list
            const index = retriviedData.value.data.findIndex(p => p.id === participant.id);
            if (index !== -1) {
                retriviedData.value.data[index].active = response.data.data.active;
            }
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

const onPageChange = (event) => {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    getData(currentPage.value);
};

const debouncedSearch = debounce(() => {
    getData(currentPage.value);
}, 300);

watch(searchQuery, debouncedSearch);

onMounted(() => {
    getData();
});
</script>

<template>
    <div class="flex flex-col md:flex-row gap-12 min-h-screen items-center justify-center" v-if="isLoadingDiv">
        <div class="w-full">
            <div class="flex flex-col gap-4 text-center">
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />
                <p>Por Favor Aguarde...</p>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row gap-12" v-else>
        <div class="w-full">
            <div class="card flex flex-col gap-4">
                <div class="font-semibold text-xl">Participantes</div>
                <DataTable
                    :value="retriviedData.data"
                    :paginator="true"
                    :rows="rowsPerPage"
                    :totalRecords="totalRecords"
                    dataKey="id"
                    :lazy="true"
                    :rowHover="true"
                    :loading="isLoadingDiv"
                    :first="(currentPage - 1) * rowsPerPage"
                    :onPage="onPageChange"
                    showGridlines
                    responsiveLayout="scroll"
                >
                    <template #header>
                        <div class="flex justify-between flex-wrap gap-2">
                            <router-link to="/admin/participants/create">
                                <Button label="Novo Participante" class="mr-2 mb-2">
                                    <i class="pi pi-plus mr-2"></i>
                                    Novo Registro
                                </Button>
                            </router-link>
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="searchQuery" placeholder="Pesquisar participantes..." />
                            </IconField>
                        </div>
                    </template>
                    <template #empty>Nenhum participante encontrado.</template>
                    <template #loading>Carregando, por favor espere.</template>
                    
                    <Column header="ID" style="min-width: 5rem">
                        <template #body="{ data }">
                            {{ data.id }}
                        </template>
                    </Column>
                    
                    <Column header="Foto" style="min-width: 8rem">
                        <template #body="{ data }">
                            <img 
                                :src="data.photo_url || '/votaaqui/assets/img/events/gate1.jpeg'" 
                                :alt="data.name"
                                class="w-10 h-10 rounded-full object-cover"
                            />
                        </template>
                    </Column>
                    
                    <Column header="Nome" style="min-width: 12rem">
                        <template #body="{ data }">
                            <div>
                                <div class="font-medium">{{ data.name }}</div>
                                <div class="text-sm text-gray-500" v-if="data.stage_name">
                                    "{{ data.stage_name }}"
                                </div>
                            </div>
                        </template>
                    </Column>
                    
                    <Column header="Código" style="min-width: 8rem">
                        <template #body="{ data }">
                            <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                {{ data.voting_code }}
                            </span>
                        </template>
                    </Column>
                    
                    <Column header="Idade/Sexo" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div>
                                <div v-if="data.age">{{ data.age }} anos</div>
                                <div class="text-sm text-gray-500">{{ getGenderText(data.gender) }}</div>
                            </div>
                        </template>
                    </Column>
                    
                    <Column header="Localização" style="min-width: 12rem">
                        <template #body="{ data }">
                            <div>
                                <div>{{ data.city }}</div>
                                <div class="text-sm text-gray-500">{{ data.province }}</div>
                            </div>
                        </template>
                    </Column>
                    
                    <Column header="Status" style="min-width: 8rem">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1">
                                <Tag :value="getStatusText(data.active)" :severity="getSeverity(data.active)" />
                                <div v-if="data.eliminated_episode" class="text-xs text-red-500">
                                    Eliminado EP{{ data.eliminated_episode.episode_number }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    
                    <Column header="Data" dataType="date" style="min-width: 10rem">
                        <template #body="{ data }">
                            {{ moment(data.created_at).format('DD/MM/YYYY HH:mm') }}
                        </template>
                    </Column>
                    
                    <Column header="Ações" style="min-width: 12rem">
                        <template #body="{ data }">
                            <div class="flex gap-2">
                                <router-link :to="'/admin/participants/' + data.id">
                                    <Button icon="pi pi-eye" size="small" severity="info" outlined 
                                           v-tooltip.top="'Visualizar'" />
                                </router-link>
                                
                                <router-link :to="'/admin/participants/' + data.id + '/edit'">
                                    <Button icon="pi pi-file-edit" size="small" severity="warning" outlined 
                                           v-tooltip.top="'Editar'" />
                                </router-link>
                                
                                <Button 
                                    :icon="data.active ? 'pi pi-eye-slash' : 'pi pi-eye'"
                                    size="small" 
                                    :severity="data.active ? 'warning' : 'success'" 
                                    outlined
                                    @click="toggleStatus(data)"
                                    :v-tooltip.top="data.active ? 'Desativar' : 'Ativar'"
                                />
                                
                                <Button 
                                    icon="pi pi-trash" 
                                    size="small" 
                                    severity="danger" 
                                    outlined
                                    @click="confirmDeletion(data.id)"
                                    v-tooltip.top="'Excluir'"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
    
    <Dialog header="Confirmação" v-model:visible="displayConfirmation" :style="{ width: '350px' }" :modal="true">
        <div class="flex align-items-center justify-content-center">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span>Tem certeza que deseja excluir este participante?</span>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" @click="closeConfirmation" class="p-button-text" />
            <Button label="Sim" icon="pi pi-check" @click="deleteData" class="p-button-text" autofocus 
                   :loading="loadingButtonDelete" />
        </template>
    </Dialog>
</template>

<style scoped>
.font-mono {
    font-family: 'Courier New', monospace;
}
</style>
