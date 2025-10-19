<template>
  <div class="page">
    <Header 
      :user="user" 
      @toggle-sidebar="toggleSidebar"
    />

    <!-- Toast Container -->
    <ToastContainer position="top-right" />

    <aside :class="['app-sidebar', { 'sidebar-open': sidebarOpen }]" id="sidebar">
      <div class="main-sidebar-header">
        <a href="/" class="header-logo">
          <h3 class="text-xl font-bold text-primary">Aplanilha</h3>
        </a>
      </div>

      <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
          <div class="slide-left" id="slide-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
              <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
            </svg>
          </div>
          <ul class="main-menu">
            <li class="slide__category"><span class="category-name">Principal</span></li>
            
            <li class="slide">
              <a href="/" class="side-menu__item" :class="{ 'active': $page.url === '/' }">
                <i class="bx bx-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
              </a>
            </li>

            <li class="slide">
              <a href="/users" class="side-menu__item" :class="{ 'active': $page.url.startsWith('/users') }">
                <i class="bx bx-user side-menu__icon"></i>
                <span class="side-menu__label">Usuários</span>
              </a>
            </li>

            <li class="slide">
              <a href="/roles" class="side-menu__item" :class="{ 'active': $page.url.startsWith('/roles') }">
                <i class="bx bx-shield side-menu__icon"></i>
                <span class="side-menu__label">Funções</span>
              </a>
            </li>

            <li class="slide__category"><span class="category-name">Configurações</span></li>

            <li class="slide">
              <a href="/settings" class="side-menu__item" :class="{ 'active': $page.url.startsWith('/settings') }">
                <i class="bx bx-cog side-menu__icon"></i>
                <span class="side-menu__label">Configurações</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <div :class="['content', { 'sidebar-open': sidebarOpen }]">
      <div class="main-content">
        <div class="container-fluid">
          <div class="page-header">
            <div>
              <h1 class="page-title">{{ title }}</h1>
              <p class="page-description">{{ description }}</p>
            </div>
          </div>
          
          <slot />
        </div>
      </div>
    </div>

    <div 
      v-if="sidebarOpen" 
      @click="closeSidebar"
      class="sidebar-overlay"
    ></div>
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import ToastContainer from '@/Components/ToastContainer.vue'
import { ref } from 'vue'

const props = defineProps({
  title: String,
  description: String,
  user: Object
})

const sidebarOpen = ref(false)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebar = () => {
  sidebarOpen.value = false
}
</script>

<style scoped>
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 998;
  display: none;
}

@media (max-width: 767px) {
  .sidebar-overlay {
    display: block;
  }
}
</style>