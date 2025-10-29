<template>
  <div class="page">
    <!-- Header Superior -->
    <Header 
      :user="user" 
      @toggle-sidebar="toggleSidebar"
    />

    <!-- Toast Container -->
    <ToastContainer position="top-right" />

    <!-- Sidebar -->
    <Sidebar
      :is-collapsed="isSidebarCollapsed"
      :is-hovered="isSidebarHovered"
      :menu-items="menuItems"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
      @link-click="keepSidebarOpenOnNavigate"
    />

    <!-- Botão fixo de abrir/fechar -->
    <button
      class="sidebar-toggle-btn"
      @click="toggleSidebar"
      :class="{ 'sidebar-expanded': !isSidebarCollapsed || isSidebarHovered }"
      :aria-label="(isSidebarCollapsed && !isSidebarHovered) ? 'Abrir sidebar' : 'Fechar sidebar'"
    >
      <i v-if="isSidebarCollapsed && !isSidebarHovered" class="bx bx-menu"></i>
      <i v-else class="bx bx-x"></i>
    </button>

    <!-- Conteúdo principal -->
    <div 
      :class="[
        'content', 
        { 
          'content-expanded': !isSidebarCollapsed || isSidebarHovered,
          'content-collapsed': isSidebarCollapsed && !isSidebarHovered
        }
      ]"
    >
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

    <!-- Overlay para mobile -->
    <div 
      v-if="!isSidebarCollapsed && isMobile"
      @click="collapseSidebar"
      class="sidebar-overlay"
    ></div>
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Sidebar from '@/Components/Sidebar.vue'
import ToastContainer from '@/Components/ToastContainer.vue'
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
  title: String,
  description: String,
  user: Object
})

// Estado da sidebar
const isSidebarCollapsed = ref(true)
const isSidebarHovered = ref(false)
const isMobile = ref(false)

// Itens do menu
const menuItems = [
  { label: 'Home', icon: 'bx bx-home', route: '/' },
  {
    label: 'Administração',
    icon: 'bx bx-cog',
    children: [
      { label: 'Usuários', icon: 'bx bx-user', route: '/users' },
      { label: 'Funções', icon: 'bx bx-shield', route: '/roles' }
    ]
  },
  {
    label: 'Relatórios',
    icon: 'bx bx-bar-chart',
    children: [
      { label: 'Financeiro', icon: 'bx bx-wallet', route: '/reports/financial' },
      { label: 'Projetos', icon: 'bx bx-folder', route: '/reports/projects' }
    ]
  }
]

// Funções da sidebar
const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  if (isSidebarCollapsed.value) isSidebarHovered.value = false
}

const handleMouseEnter = () => {
  if (isSidebarCollapsed.value) isSidebarHovered.value = true
}

const handleMouseLeave = () => {
  if (isSidebarCollapsed.value) isSidebarHovered.value = false
}

const collapseSidebar = () => {
  isSidebarCollapsed.value = true
}

const keepSidebarOpenOnNavigate = () => {
  if (isSidebarCollapsed.value) {
    isSidebarCollapsed.value = false
    isSidebarHovered.value = false
    localStorage.setItem('sidebar-fixed-open', 'true')
  }
}

// Responsividade
onMounted(() => {
  const saved = localStorage.getItem('sidebar-fixed-open')
  if (saved === 'true') {
    isSidebarCollapsed.value = false
    isSidebarHovered.value = false
  }

  const checkMobile = () => {
    isMobile.value = window.innerWidth <= 767
  }
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

// Persistência
watch(isSidebarCollapsed, (val) => {
  localStorage.setItem('sidebar-fixed-open', (!val).toString())
})
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

/* Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 998;
}

/* Conteúdo ajustável */
.content {
  transition: margin-left 0.3s ease;
  padding-top: 4rem;
}

/* Quando a sidebar está expandida */
.content-expanded {
  margin-left: 250px;
}

/* Quando a sidebar está colapsada */
.content-collapsed {
  margin-left: 72px;
}

/* Mobile */
@media (max-width: 767px) {
  .content-expanded,
  .content-collapsed {
    margin-left: 0;
  }
}

/* Botão fixo de toggle */
.sidebar-toggle-btn {
  position: fixed;
  top: 1rem;
  left: 88px;
  z-index: 1000;
  background: transparent;
  border: 0;
  padding: 0;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  cursor: pointer;
  color: #9aa0a6;
  transition: left 0.3s ease, background-color 0.2s ease, color 0.2s ease;
}

/* Quando a sidebar está aberta */
.sidebar-toggle-btn.sidebar-expanded {
  left: 266px;
}

/* Efeitos */
.sidebar-toggle-btn:hover,
.sidebar-toggle-btn:focus-visible {
  background-color: rgba(255, 255, 255, 0.06);
  color: #e5e7eb;
  outline: none;
}

.sidebar-toggle-btn i {
  font-size: 24px;
}
</style>
