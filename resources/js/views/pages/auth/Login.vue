<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const toast = useToast();
const email = ref('');
const password = ref('');
const checked = ref(false);
const submitted = ref(false);
const errorMessage = ref('');

const loginUser = () => {
    submitted.value = true;
    errorMessage.value = '';

    axios
        .post(`/api/login`, {
            email: email.value.toLowerCase(),
            password: password.value
        })
        .then((response) => {
            localStorage.setItem('token', response.data.access_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
            toast.add({ severity: 'success', summary: 'Sessão iniciada', detail: 'A entrar no painel.', life: 2500 });

            if (response.data.user.role_id == 1 || response.data.user.role_id == 2) {
                window.location.href = '/admin/dashboard';
            } else if (response.data.user.role_id == 3) {
                window.location.href = '/judge/dashboard';
            }
        })
        .catch((error) => {
            errorMessage.value = error.response?.data?.message || 'Não foi possível iniciar sessão.';
            toast.add({ severity: 'error', summary: errorMessage.value, life: 3000 });
        })
        .finally(() => {
            submitted.value = false;
        });
};
</script>

<template>
    <div class="login">
        <section class="login-visual" aria-hidden="true">
            <img class="login-banner" src="/votaaqui/assets/img/events/karaoke.png" alt="">
            <p>Acesso da organização ao Sofala Talents.</p>
        </section>

        <section class="login-panel">
            <div class="login-form-wrap">
                <img class="login-logo" src="/votaaqui/assets/img/logo.png" alt="Sofala Talents">
                <h1>Entrar</h1>
                <p class="login-lead">Use o email e a palavra-passe da sua conta.</p>

                <p v-if="errorMessage" class="login-error" role="alert">{{ errorMessage }}</p>

                <form @submit.prevent="loginUser">
                    <label for="email">Email</label>
                    <InputText
                        id="email"
                        v-model="email"
                        type="email"
                        autocomplete="username"
                        placeholder="nome@email.com"
                        required
                    />

                    <label for="password">Palavra-passe</label>
                    <Password
                        id="password"
                        v-model="password"
                        :feedback="false"
                        :toggleMask="true"
                        placeholder="Palavra-passe"
                        autocomplete="current-password"
                        fluid
                        required
                    />

                    <label class="login-remember" for="rememberme">
                        <Checkbox v-model="checked" inputId="rememberme" binary />
                        <span>Lembrar-me neste dispositivo</span>
                    </label>

                    <Button
                        type="submit"
                        class="login-submit"
                        :label="submitted ? 'A entrar...' : 'Entrar'"
                        :loading="submitted"
                    />
                </form>
            </div>
        </section>
    </div>
</template>

<style scoped>
.login {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #fff;
    color: #111;
}

.login-visual {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 2rem;
    padding: 3rem;
    color: #fff;
    background: #14110f;
}

.login-banner {
    width: 100%;
    height: auto;
    display: block;
}

.login-visual p {
    margin: 0;
    max-width: 22rem;
    font-size: 1.25rem;
    line-height: 1.45;
}

.login-logo {
    display: block;
    width: 168px;
    height: auto;
    margin-bottom: 1.5rem;
}

.login-panel {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
}

.login-form-wrap {
    width: 100%;
    max-width: 380px;
}

h1 {
    margin: 0 0 0.4rem;
    font-size: 2rem;
    font-weight: 600;
    color: #111;
}

.login-lead {
    margin: 0 0 1.75rem;
    color: #5c564f;
}

form {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

label {
    margin-top: 0.7rem;
    font-size: 0.92rem;
    font-weight: 600;
    color: #111;
}

.login-remember {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    margin: 0.9rem 0 1.1rem;
    font-weight: 500;
    cursor: pointer;
}

.login-error {
    margin: 0 0 1rem;
    padding: 0.75rem 0.9rem;
    border: 1px solid #e4e0da;
    background: #f7f5f2;
    color: #111;
}

:deep(.p-inputtext),
:deep(.p-password-input) {
    width: 100%;
    border: 1px solid #d9d3cb;
    border-radius: 8px;
    padding: 0.8rem 0.9rem;
    color: #111;
    background: #fff;
    box-shadow: none;
}

:deep(.p-inputtext:enabled:focus),
:deep(.p-password-input:enabled:focus) {
    border-color: #111;
    box-shadow: none;
}

:deep(.p-password) {
    width: 100%;
}

:deep(.login-submit) {
    width: 100%;
    margin-top: 0.4rem;
    border: 0;
    border-radius: 8px;
    background: #111;
    color: #fff;
    padding: 0.85rem 1rem;
    font-weight: 600;
}

:deep(.login-submit:hover) {
    background: #2a2a2a;
}

@media (max-width: 860px) {
    .login {
        grid-template-columns: 1fr;
    }

    .login-visual {
        display: none;
    }

    .login-panel {
        min-height: 100vh;
    }
}
</style>
