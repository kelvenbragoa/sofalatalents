<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { ref, onBeforeMount } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import router from '../../../router';


const toast = useToast();
const email = ref('');
const password = ref('');
const checked = ref(false);
const submitted = ref(false);
const errorMessage = ref('');

const loginUser = () => {
    submitted.value = true;
    // axios.get(`${baseURL2}/sanctum/csrf-cookie`).then(() => {

    axios
        .post(`/api/login`, {
            email: email.value.toLowerCase(),
            password: password.value
        })
        .then((response) => {
            localStorage.setItem('token', response.data.access_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
            toast.add({ severity: 'success', summary: 'Successo', detail: 'Message Detail', life: 3000 });

            if (response.data.user.role_id == 1 || response.data.user.role_id == 2) {
                window.location.href = '/admin/dashboard';
            } else if (response.data.user.role_id == 3) {
                window.location.href = '/judge/dashboard';
            };
           
        })
        .catch((error) => {
            errorMessage.value = error.response.data.message;
            toast.add({ severity: 'error', summary: `${error.response.data.message}`, detail: 'Message Detail', life: 3000 });
        })
        .finally(() => {
            submitted.value = false;
        });
    // })
};

onBeforeMount(() => {
    const token = localStorage.getItem('token');
    if (token) {
        // User is authenticated, proceed to the route
        // router.replace('/dashboard');
    }
});


</script>

<template>
    <div class="login-container">
        <!-- Background with red gradient and pattern -->
        <div class="login-background">
            <div class="background-pattern"></div>
            <div class="floating-elements">
                <div class="floating-element" v-for="i in 6" :key="i"></div>
            </div>
        </div>

        <!-- Main login form container -->
        <div class="login-wrapper">
            <div class="login-card">
                <!-- Header section with logo and title -->
                <div class="login-header">
                    <div class="logo-container">
                        <svg viewBox="0 0 54 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-svg">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M17.1637 19.2467C17.1566 19.4033 17.1529 19.561 17.1529 19.7194C17.1529 25.3503 21.7203 29.915 27.3546 29.915C32.9887 29.915 37.5561 25.3503 37.5561 19.7194C37.5561 19.5572 37.5524 19.3959 37.5449 19.2355C38.5617 19.0801 39.5759 18.9013 40.5867 18.6994L40.6926 18.6782C40.7191 19.0218 40.7326 19.369 40.7326 19.7194C40.7326 27.1036 34.743 33.0896 27.3546 33.0896C19.966 33.0896 13.9765 27.1036 13.9765 19.7194C13.9765 19.374 13.9896 19.0316 14.0154 18.6927L14.0486 18.6994C15.0837 18.9062 16.1223 19.0886 17.1637 19.2467ZM33.3284 11.4538C31.6493 10.2396 29.5855 9.52381 27.3546 9.52381C25.1195 9.52381 23.0524 10.2421 21.3717 11.4603C20.0078 11.3232 18.6475 11.1387 17.2933 10.907C19.7453 8.11308 23.3438 6.34921 27.3546 6.34921C31.36 6.34921 34.9543 8.10844 37.4061 10.896C36.0521 11.1292 34.692 11.3152 33.3284 11.4538ZM43.826 18.0518C43.881 18.6003 43.9091 19.1566 43.9091 19.7194C43.9091 28.8568 36.4973 36.2642 27.3546 36.2642C18.2117 36.2642 10.8 28.8568 10.8 19.7194C10.8 19.1615 10.8276 18.61 10.8816 18.0663L7.75383 17.4411C7.66775 18.1886 7.62354 18.9488 7.62354 19.7194C7.62354 30.6102 16.4574 39.4388 27.3546 39.4388C38.2517 39.4388 47.0855 30.6102 47.0855 19.7194C47.0855 18.9439 47.0407 18.1789 46.9536 17.4267L43.826 18.0518ZM44.2613 9.54743L40.9084 10.2176C37.9134 5.95821 32.9593 3.1746 27.3546 3.1746C21.7442 3.1746 16.7856 5.96385 13.7915 10.2305L10.4399 9.56057C13.892 3.83178 20.1756 0 27.3546 0C34.5281 0 40.8075 3.82591 44.2613 9.54743Z"
                                fill="currentColor"
                            />
                        </svg>
                    </div>
                    <h1 class="app-title">Vota Aqui</h1>
                    <p class="app-subtitle">Sistema de Votação</p>
                </div>

                <!-- Error message -->
                <div v-if="errorMessage" class="error-message">
                    <i class="pi pi-exclamation-triangle"></i>
                    {{ errorMessage }}
                </div>

                <!-- Login form -->
                <form class="login-form" @submit.prevent="loginUser">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="pi pi-envelope"></i>
                            Email
                        </label>
                        <InputText 
                            id="email" 
                            type="email" 
                            placeholder="Digite seu email" 
                            class="form-input" 
                            v-model="email" 
                            required 
                        />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="pi pi-lock"></i>
                            Senha
                        </label>
                        <Password 
                            id="password" 
                            v-model="password" 
                            placeholder="Digite sua senha" 
                            :toggleMask="true" 
                            class="form-input password-input" 
                            :feedback="false"
                            fluid
                            required
                        />
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <Checkbox v-model="checked" id="rememberme" binary class="custom-checkbox" />
                            <label for="rememberme" class="checkbox-label">Lembrar-me</label>
                        </div>
                        <a href="#" class="forgot-password">Esqueci minha senha</a>
                    </div>

                    <div class="form-submit">
                        <Button 
                            v-if="!submitted"
                            label="Entrar" 
                            class="login-button" 
                            @click="loginUser"
                            icon="pi pi-sign-in"
                            iconPos="left"
                        />
                        <div v-else class="loading-container">
                            <ProgressSpinner 
                                class="login-spinner" 
                                strokeWidth="6" 
                                fill="transparent" 
                                animationDuration="1s" 
                            />
                            <span class="loading-text">Entrando...</span>
                        </div>
                    </div>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <p>&copy; 2025 Vota Aqui - Todos os direitos reservados</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Container principal */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Background com gradiente vermelho */
.login-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ee6666 0%, #b91c1c 50%, #991b1b 100%);
    z-index: 1;
}

/* Padrão de fundo */
.background-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.1;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

/* Elementos flutuantes animados */
.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.floating-element {
    position: absolute;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 15s infinite linear;
}

.floating-element:nth-child(1) { left: 10%; animation-delay: 0s; }
.floating-element:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 12s; }
.floating-element:nth-child(3) { left: 60%; animation-delay: 4s; }
.floating-element:nth-child(4) { left: 80%; animation-delay: 6s; animation-duration: 18s; }
.floating-element:nth-child(5) { left: 70%; animation-delay: 8s; }
.floating-element:nth-child(6) { left: 30%; animation-delay: 10s; animation-duration: 14s; }

@keyframes float {
    0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) rotate(360deg);
        opacity: 0;
    }
}

/* Wrapper do card de login */
.login-wrapper {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
    padding: 1rem;
}

/* Card principal */
.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 3rem;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: slideIn 0.8s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Header do login */
.login-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.logo-container {
    display: inline-block;
    margin-bottom: 1.5rem;
}

.logo-svg {
    width: 64px;
    height: 48px;
    color: #dc2626;
    filter: drop-shadow(0 4px 8px rgba(220, 38, 38, 0.3));
}

.app-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.app-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
    font-weight: 500;
}

/* Mensagem de erro */
.error-message {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #fca5a5;
    color: #dc2626;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.error-message i {
    font-size: 1.25rem;
}

/* Formulário */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label i {
    color: #dc2626;
    font-size: 1.1rem;
}

/* Inputs customizados */
:deep(.form-input) {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

:deep(.form-input:focus) {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    outline: none;
}

:deep(.form-input:hover) {
    border-color: #fca5a5;
}

/* Password input específico */
:deep(.password-input .p-password-input) {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
}

:deep(.password-input .p-password-input:focus) {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    outline: none;
}

:deep(.password-input .p-password-toggle) {
    color: #6b7280;
    margin-right: 0.75rem;
}

:deep(.password-input .p-password-toggle:hover) {
    color: #dc2626;
}

/* Opções do formulário */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

:deep(.custom-checkbox) {
    width: 1.25rem;
    height: 1.25rem;
}

:deep(.custom-checkbox .p-checkbox-box) {
    border: 2px solid #e5e7eb;
    border-radius: 4px;
    background: white;
    transition: all 0.3s ease;
}

:deep(.custom-checkbox .p-checkbox-box.p-highlight) {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border-color: #dc2626;
}

.checkbox-label {
    font-size: 0.95rem;
    color: #374151;
    cursor: pointer;
    font-weight: 500;
}

.forgot-password {
    color: #dc2626;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: color 0.3s ease;
}

.forgot-password:hover {
    color: #b91c1c;
    text-decoration: underline;
}

/* Botão de login */
.form-submit {
    margin-top: 1rem;
}

:deep(.login-button) {
    width: 100%;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
}

:deep(.login-button:hover) {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
}

:deep(.login-button:active) {
    transform: translateY(0);
}

/* Estado de carregamento */
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
}

:deep(.login-spinner) {
    width: 40px;
    height: 40px;
}

:deep(.login-spinner .p-progress-spinner-circle) {
    stroke: #dc2626;
    stroke-width: 6;
}

.loading-text {
    color: #6b7280;
    font-weight: 500;
    font-size: 1rem;
}

/* Footer */
.login-footer {
    margin-top: 2rem;
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.login-footer p {
    color: #9ca3af;
    font-size: 0.875rem;
    margin: 0;
}

/* Responsividade */
@media (max-width: 768px) {
    .login-wrapper {
        padding: 0.5rem;
        max-width: 100%;
    }

    .login-card {
        padding: 2rem 1.5rem;
        margin: 1rem 0;
        border-radius: 20px;
    }

    .app-title {
        font-size: 2rem;
    }

    .form-options {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .login-card {
        padding: 1.5rem 1rem;
    }

    .app-title {
        font-size: 1.75rem;
    }

    :deep(.form-input),
    :deep(.password-input .p-password-input) {
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
    }
}

/* Animações adicionais */
.login-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 32px 64px -12px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(255, 255, 255, 0.3);
}

/* Transições suaves para todos os elementos */
* {
    transition: all 0.3s ease;
}
</style>
