<template>
  <div class="page">
    <ToastContainer position="top-right" />

    <!-- Sidebar -->
    <Sidebar 
      :is-collapsed="isSidebarCollapsed" 
      :is-hovered="isSidebarHovered"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
      @link-click="keepSidebarOpenOnNavigate"
    />

    <!-- Botão fixo de toggle (descolado da sidebar) -->
    <button
    class="sidebar-toggle-btn"
    @click="toggleSidebarFixed"
    :aria-label="(isSidebarCollapsed && !isSidebarHovered) ? 'Abrir sidebar' : 'Fechar sidebar'"
    :class="{ 'sidebar-expanded': !isSidebarCollapsed || isSidebarHovered }"
>
    <i v-if="isSidebarCollapsed && !isSidebarHovered" class="bx bx-menu"></i>
    <i v-else class="bx bx-x"></i>
</button>

    <!-- Conteúdo -->
    <div 
      :class="['content', { 
        'sidebar-expanded': isSidebarHovered || !isSidebarCollapsed 
      }]"
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

    <!-- Overlay Mobile -->
    <div 
      v-if="!isSidebarCollapsed && isMobile"
      @click="collapseSidebar"
      class="sidebar-overlay"
    ></div>
  </div>
</template>

<script setup>
import ToastContainer from '@/Components/ToastContainer.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
  title: String,
  description: String,
  user: Object
})

const isSidebarCollapsed = ref(true)
const isSidebarHovered = ref(false)
const isMobile = ref(false)

const toggleSidebarFixed = () => {
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

watch(isSidebarCollapsed, (val) => {
  localStorage.setItem('sidebar-fixed-open', (!val).toString())
})
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
}

.content {
  transition: margin-left 0.3s ease;
}

.content.sidebar-expanded {
  margin-left: 250px;
}

@media (max-width: 767px) {
  .content.sidebar-expanded {
    margin-left: 0;
  }
}

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
    transition: left 0.3s ease, background-color 0.2s ease, color 0.2s ease;
    color: #9aa0a6;          
    box-shadow: none;     
}

.sidebar-toggle-btn.sidebar-expanded {
    left: 266px;             
}

.sidebar-toggle-btn:hover,
.sidebar-toggle-btn:focus-visible {
    background-color: rgba(255, 255, 255, 0.06);
    color: #e5e7eb;          
    outline: none;
}

.sidebar-toggle-btn i {
    font-size: 24px;         
    color: #9aa0a6;
    line-height: 1;
}
</style>
