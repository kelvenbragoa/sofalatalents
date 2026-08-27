<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();
const loadingButtonSave = ref(false);
const photoFile = ref(null);
const photoPreview = ref(null);

const form = reactive({
    name: '',
    stage_name: '',
    voting_code: '',
    age: null,
    gender: '',
    city: '',
    province: '',
    biography: '',
    occupation: '',
    instagram: '',
    facebook: '',
    tiktok: '',
    photo: null,
    active: true
});

const errors = ref({});

const genderOptions = [
    { label: 'Masculino', value: 'M' },
    { label: 'Feminino', value: 'F' },
    { label: 'Outro', value: 'Other' }
];

const provinces = [
    'Cabo Delgado', 'Gaza', 'Inhambane', 'Manica', 'Maputo Cidade', 
    'Maputo Província', 'Nampula', 'Niassa', 'Sofala', 'Tete', 'Zambézia'
];

const onPhotoSelect = (event) => {
    const file = event.files[0];
    if (file) {
        form.photo = file;
        photoFile.value = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const clearPhoto = () => {
    form.photo = null;
    photoFile.value = null;
    photoPreview.value = null;
};

const generateVotingCode = () => {
    // Generate a unique 4-character code
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 4; i++) {
        code += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    form.voting_code = code;
};

const submitForm = () => {
    loadingButtonSave.value = true;
    errors.value = {};

    const formData = new FormData();
    
    // Add all form fields
    Object.keys(form).forEach(key => {
        if (form[key] !== null && form[key] !== '' && key !== 'photo') {
            formData.append(key, form[key]);
        }
    });
    
    // Add photo if selected
    if (form.photo) {
        formData.append('photo', form.photo);
    }

    axios.post('/api/web/participants', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then((response) => {
        toast.add({ 
            severity: 'success', 
            summary: 'Sucesso', 
            detail: 'Participante criado com sucesso', 
            life: 3000 
        });
        router.push('/admin/participants');
    })
    .catch((error) => {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            toast.add({ 
                severity: 'error', 
                summary: 'Erro de Validação', 
                detail: 'Por favor, corrija os campos destacados', 
                life: 5000 
            });
        } else {
            toast.add({ 
                severity: 'error', 
                summary: 'Erro', 
                detail: error.response?.data?.message || 'Erro ao criar participante', 
                life: 5000 
            });
        }
    })
    .finally(() => {
        loadingButtonSave.value = false;
    });
};

const goBack = () => {
    router.push('/admin/participants');
};

onMounted(() => {
    generateVotingCode();
});
</script>

<template>
    <div class="flex flex-col md:flex-row gap-12">
        <div class="w-full">
            <div class="card flex flex-col gap-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-semibold text-xl">Novo Participante</div>
                    <Button 
                        icon="pi pi-arrow-left" 
                        label="Voltar" 
                        severity="secondary" 
                        outlined 
                        @click="goBack"
                    />
                </div>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informações Básicas -->
                        <div class="col-span-full">
                            <h3 class="text-lg font-medium mb-4">Informações Básicas</h3>
                        </div>

                        <!-- Foto -->
                        <div class="col-span-full">
                            <label class="block text-sm font-medium mb-2">Foto do Participante</label>
                            <div class="flex flex-col lg:flex-row gap-4 items-start">
                                <!-- Preview -->
                                <div v-if="photoPreview" class="relative">
                                    <img 
                                        :src="photoPreview" 
                                        alt="Preview" 
                                        class="w-32 h-32 object-cover rounded-lg border"
                                    />
                                    <Button 
                                        icon="pi pi-times" 
                                        class="absolute -top-2 -right-2 p-button-rounded p-button-danger p-button-sm"
                                        @click="clearPhoto"
                                    />
                                </div>
                                
                                <!-- Upload -->
                                <div class="flex-1">
                                    <FileUpload
                                        mode="basic"
                                        accept="image/*"
                                        :maxFileSize="2000000"
                                        :auto="false"
                                        chooseLabel="Selecionar Foto"
                                        @select="onPhotoSelect"
                                        class="w-full"
                                    />
                                    <small class="text-gray-500">Máximo 2MB. Formatos: JPG, PNG</small>
                                </div>
                            </div>
                        </div>

                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2">Nome Completo *</label>
                            <InputText
                                id="name"
                                v-model="form.name"
                                :class="{ 'p-invalid': errors.name }"
                                placeholder="Digite o nome completo"
                                class="w-full"
                            />
                            <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
                        </div>

                        <!-- Nome Artístico -->
                        <div>
                            <label for="stage_name" class="block text-sm font-medium mb-2">Nome Artístico</label>
                            <InputText
                                id="stage_name"
                                v-model="form.stage_name"
                                :class="{ 'p-invalid': errors.stage_name }"
                                placeholder="Nome artístico ou apelido"
                                class="w-full"
                            />
                            <small v-if="errors.stage_name" class="p-error">{{ errors.stage_name[0] }}</small>
                        </div>

                        <!-- Código de Votação -->
                        <div>
                            <label for="voting_code" class="block text-sm font-medium mb-2">Código de Votação *</label>
                            <div class="flex gap-2">
                                <InputText
                                    id="voting_code"
                                    v-model="form.voting_code"
                                    :class="{ 'p-invalid': errors.voting_code }"
                                    placeholder="Código único"
                                    class="flex-1"
                                    maxlength="4"
                                />
                                <Button 
                                    icon="pi pi-refresh" 
                                    @click="generateVotingCode"
                                    type="button"
                                    outlined
                                    v-tooltip.top="'Gerar novo código'"
                                />
                            </div>
                            <small v-if="errors.voting_code" class="p-error">{{ errors.voting_code[0] }}</small>
                            <small v-else class="text-gray-500">Código usado para votação SMS/WhatsApp</small>
                        </div>

                        <!-- Idade -->
                        <div>
                            <label for="age" class="block text-sm font-medium mb-2">Idade</label>
                            <InputNumber
                                id="age"
                                v-model="form.age"
                                :class="{ 'p-invalid': errors.age }"
                                placeholder="Idade"
                                :min="16"
                                :max="100"
                                class="w-full"
                            />
                            <small v-if="errors.age" class="p-error">{{ errors.age[0] }}</small>
                        </div>

                        <!-- Sexo -->
                        <div>
                            <label for="gender" class="block text-sm font-medium mb-2">Sexo</label>
                            <Dropdown
                                id="gender"
                                v-model="form.gender"
                                :options="genderOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o sexo"
                                :class="{ 'p-invalid': errors.gender }"
                                class="w-full"
                            />
                            <small v-if="errors.gender" class="p-error">{{ errors.gender[0] }}</small>
                        </div>

                        <!-- Cidade -->
                        <div>
                            <label for="city" class="block text-sm font-medium mb-2">Cidade</label>
                            <InputText
                                id="city"
                                v-model="form.city"
                                :class="{ 'p-invalid': errors.city }"
                                placeholder="Cidade de origem"
                                class="w-full"
                            />
                            <small v-if="errors.city" class="p-error">{{ errors.city[0] }}</small>
                        </div>

                        <!-- Província -->
                        <div>
                            <label for="province" class="block text-sm font-medium mb-2">Província</label>
                            <Dropdown
                                id="province"
                                v-model="form.province"
                                :options="provinces"
                                placeholder="Selecione a província"
                                :class="{ 'p-invalid': errors.province }"
                                class="w-full"
                            />
                            <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
                        </div>

                        <!-- Ocupação -->
                        <div>
                            <label for="occupation" class="block text-sm font-medium mb-2">Ocupação</label>
                            <InputText
                                id="occupation"
                                v-model="form.occupation"
                                :class="{ 'p-invalid': errors.occupation }"
                                placeholder="Profissão ou ocupação"
                                class="w-full"
                            />
                            <small v-if="errors.occupation" class="p-error">{{ errors.occupation[0] }}</small>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Status</label>
                            <div class="flex items-center">
                                <Checkbox
                                    id="active"
                                    v-model="form.active"
                                    :binary="true"
                                />
                                <label for="active" class="ml-2">Participante ativo</label>
                            </div>
                        </div>

                        <!-- Biografia -->
                        <div class="col-span-full">
                            <label for="biography" class="block text-sm font-medium mb-2">Biografia</label>
                            <Textarea
                                id="biography"
                                v-model="form.biography"
                                :class="{ 'p-invalid': errors.biography }"
                                placeholder="Conte sobre o participante..."
                                rows="4"
                                class="w-full"
                            />
                            <small v-if="errors.biography" class="p-error">{{ errors.biography[0] }}</small>
                        </div>

                        <!-- Redes Sociais -->
                        <div class="col-span-full">
                            <h3 class="text-lg font-medium mb-4">Redes Sociais</h3>
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="instagram" class="block text-sm font-medium mb-2">Instagram</label>
                            <InputText
                                id="instagram"
                                v-model="form.instagram"
                                :class="{ 'p-invalid': errors.instagram }"
                                placeholder="@usuario"
                                class="w-full"
                            />
                            <small v-if="errors.instagram" class="p-error">{{ errors.instagram[0] }}</small>
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label for="facebook" class="block text-sm font-medium mb-2">Facebook</label>
                            <InputText
                                id="facebook"
                                v-model="form.facebook"
                                :class="{ 'p-invalid': errors.facebook }"
                                placeholder="Nome de usuário"
                                class="w-full"
                            />
                            <small v-if="errors.facebook" class="p-error">{{ errors.facebook[0] }}</small>
                        </div>

                        <!-- TikTok -->
                        <div>
                            <label for="tiktok" class="block text-sm font-medium mb-2">TikTok</label>
                            <InputText
                                id="tiktok"
                                v-model="form.tiktok"
                                :class="{ 'p-invalid': errors.tiktok }"
                                placeholder="@usuario"
                                class="w-full"
                            />
                            <small v-if="errors.tiktok" class="p-error">{{ errors.tiktok[0] }}</small>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end gap-3 mt-8">
                        <Button 
                            label="Cancelar" 
                            severity="secondary" 
                            outlined 
                            @click="goBack"
                            type="button"
                        />
                        <Button 
                            label="Salvar Participante" 
                            type="submit"
                            :loading="loadingButtonSave"
                            icon="pi pi-save"
                        />
                    </div>
                </form>
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

@media (min-width: 1024px) {
    .lg\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.col-span-full {
    grid-column: 1 / -1;
}

.gap-6 {
    gap: 1.5rem;
}
</style>
