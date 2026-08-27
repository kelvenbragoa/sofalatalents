<script setup>
import { useLayout } from '@/layout/composables/layout';
import AppConfigurator from './AppConfigurator.vue';
import { RouterView, RouterLink, useRouter, useRoute } from 'vue-router';

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

const router = useRouter();

const logout = () => {
    axios.post(`/api/logout`)
    .then((response) => {
        localStorage.removeItem('token'); // Remova o token armazenado
        localStorage.removeItem('user');  // Remova as informações do usuário
        router.replace('/auth/login'); // Redirecione para a página de login
    })
    .catch((error) => {
        console.error('Erro ao fazer logout:', error); // Lide com erros de logout
        localStorage.removeItem('token'); // Limpeza mesmo em caso de erro
        localStorage.removeItem('user');
        router.replace('/auth/login'); // Redirecione para a página de login
    });
};

</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/admin/dashboard" class="layout-topbar-logo">
                
                <span>VotaAqui</span>
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
                <div class="relative">
                    <!-- <button
                        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                        type="button"
                        class="layout-topbar-action layout-topbar-action-highlight"
                    >
                        <i class="pi pi-palette"></i>
                    </button> -->
                    <AppConfigurator />
                </div>
            </div>

            <button
                class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
            >
                <i class="pi pi-ellipsis-v"></i>
            </button>

            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    <!-- <button type="button" class="layout-topbar-action">
                        <i class="pi pi-calendar"></i>
                        <span>Calendar</span>
                    </button> -->
                    <button @click="logout()" type="button" class="layout-topbar-action p-button p-button-text">
                        <i class="pi pi-sign-out"></i>
                        <span>Sair</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
