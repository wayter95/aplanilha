<template>
  <div class="page">
    <!-- Header Superior -->
    <Header 
      :user="user" 
      :is-sidebar-collapsed="isSidebarCollapsed"
      :is-sidebar-hovered="isSidebarHovered"
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

    <!-- toggle moved into Header to align with logo -->

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

const isSidebarCollapsed = ref(true)
const isSidebarHovered = ref(false)
const isMobile = ref(false)

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

onMounted(() => {
    const checkMobile = () => {
        isMobile.value = window.innerWidth <= 992
    }
    checkMobile()
    window.addEventListener('resize', checkMobile)
})


watch(isSidebarCollapsed, (val) => {
  localStorage.setItem('sidebar-fixed-open', (!val).toString())
})
</script>
<style scoped>

/* Layout da página */
.page {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

/* Conteúdo ajustável */
.content {
  transition: margin-left 0.3s ease;
  padding-top: 4rem;
}

/* Sidebar expandida */
.content-expanded {
  margin-left: 250px;
}

/* Sidebar colapsada */
.content-collapsed {
  margin-left: 72px;
}

/* Keep content shifted to make the sidebar visible even on small screens */
/* Do not zero margin-left on mobile: the collapsed sidebar (72px) should remain visible */
@media (max-width: 767px) {
  .content-expanded { margin-left: 250px; }
  .content-collapsed { margin-left: 72px; }
}

/* toggle button moved into Header.vue */
</style>
