<template>
  <!-- Mobile Layout for small screens (≤ 768px) -->
  <MobileLayout 
    v-if="isMobileDevice && screenWidth <= 768"
    :user="user" 
    :menu-items="menuItems"
    @sidebar-toggle="handleMobileSidebarToggle"
  >
    <!-- Mobile content with tabs support -->
    <div class="mobile-main-content">
      <div class="mobile-container" :class="{ 'mobile-tab-active': shouldShowTabContent }">
        <!-- Page header - always visible -->
        <div class="mobile-page-header" v-if="!shouldShowTabContent">
          <div>
            <h1 class="mobile-page-title">{{ title }}</h1>
            <p class="mobile-page-description" v-if="description">{{ description }}</p>
          </div>
        </div>
        
        <!-- Regular content -->
        <div v-if="!shouldShowTabContent" class="mobile-page-content">
          <slot />
        </div>
        
        <!-- Tab content in full screen -->
        <div v-if="shouldShowTabContent && resolvedComponent" class="mobile-tab-content">
          <div class="mobile-tab-header">
            <h1 class="mobile-tab-title">{{ activeTab?.title || title }}</h1>
          </div>
          <div class="mobile-tab-body">
            <component :is="resolvedComponent" v-bind="{ ...activeTab.props, standalone: false }" :key="activeTab.key" />
          </div>
        </div>
      </div>
    </div>
  </MobileLayout>

  <!-- Desktop Layout for larger screens (original layout preserved) -->
  <div v-else class="page">
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

    <!-- Conteúdo principal -->
    <div 
      :class="[
        'content', 
        { 
          'content-expanded': !isSidebarCollapsed || isSidebarHovered,
          'content-collapsed': isSidebarCollapsed && !isSidebarHovered,
          'content-with-tabs': hasTabs
        }
      ]"
    >
      <div class="main-content">
        <div class="container-fluid" :class="{ 'tab-active': shouldShowTabContent }">
          <!-- Header sempre visível, mesmo com tab ativa -->
          <div class="page-header">
            <div>
              <h1 class="page-title">{{ shouldShowTabContent && activeTab?.title ? activeTab.title : title }}</h1>
              <p class="page-description">{{ description }}</p>
            </div>
          </div>
          <div v-if="!shouldShowTabContent">
            <slot />
          </div>
          <div v-if="shouldShowTabContent && resolvedComponent" class="tab-content-full">
            <component :is="resolvedComponent" v-bind="{ ...activeTab.props, standalone: false }" :key="activeTab.key" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Sidebar from '@/Components/Sidebar.vue'
import MobileLayout from '@/Components/MobileLayout.vue'
import ToastContainer from '@/Components/ToastContainer.vue'
import { ref, onMounted, watch, computed, shallowRef } from 'vue'
import { useTabsStore } from '@/stores/useTabsStore'
import { storeToRefs } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { useMobileDetection } from '@/composables/useMobileDetection'

const page = usePage()
const tabsStore = useTabsStore()
const { activeTab, tabs } = storeToRefs(tabsStore)
const hasTabs = computed(() => tabs.value.length > 0)

// Verifica se a URL atual corresponde ao path da tab ativa
const shouldShowTabContent = computed(() => {
    if (!activeTab.value) return false
    const currentPath = page.url.split('?')[0]
    const tabPath = activeTab.value.path.split('?')[0]
    return tabPath === currentPath
})

const resolvedComponent = shallowRef(null)

watch(activeTab, async (newTab) => {
  if (!newTab?.componentName) {
    resolvedComponent.value = null
    return
  }
  
  try {
    // Usa o sistema de registro dinâmico
    const { loadTabComponent } = await import('@/config/tabComponents')
    const component = await loadTabComponent(newTab.componentName)
    resolvedComponent.value = component
  } catch (error) {
    resolvedComponent.value = null
  }
}, { immediate: true })

const props = defineProps({
  title: String,
  description: String,
  user: Object
})

const isSidebarCollapsed = ref(true)
const isSidebarHovered = ref(false)
const { isMobileDevice, screenWidth } = useMobileDetection()

const menuItems = [
  { label: 'Home', icon: 'bx bx-home', route: '/' },
  {
    label: 'Administração',
    icon: 'bx bx-cog',
    children: [
      { label: 'Usuários', icon: 'bx bx-user', route: '/users' },
      { label: 'Funções', icon: 'bx bx-shield', route: '/roles' },
      { label: 'Modelos de Documentos', icon: 'bx bx-file', route: '/document-templates' }
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

const handleMobileSidebarToggle = (isOpen) => {
  // Handle mobile sidebar state if needed
  console.log('Mobile sidebar toggled:', isOpen)
}

onMounted(() => {
  // Load saved sidebar state for desktop
  const savedState = localStorage.getItem('sidebar-fixed-open')
  if (savedState === 'true') {
    isSidebarCollapsed.value = false
  }
})


watch(isSidebarCollapsed, (val) => {
  localStorage.setItem('sidebar-fixed-open', (!val).toString())
})
</script>
<style scoped>
/* Desktop Layout Styles (original/preserved) */
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

/* Conteúdo com tabs */
.content-with-tabs {
  padding-top: calc(4rem + 3rem);
}

/* Quando há tab ativa, ocultar padding e mostrar formulário em tela cheia */
.tab-active {
  padding: 0;
}

.tab-content-full {
  height: calc(100vh - 4rem);
  overflow-y: auto;
  padding: 1.5rem;
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

/* Mobile Layout Styles (only apply when mobile layout is active) */
.mobile-main-content {
  height: 100%;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.mobile-container {
  min-height: 100%;
  padding: 1rem;
}

.mobile-tab-active {
  padding: 0;
  height: 100%;
}

/* Mobile Page Header */
.mobile-page-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-page-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #fff;
  margin: 0 0 0.5rem 0;
}

.mobile-page-description {
  font-size: 0.875rem;
  color: #9ca3af;
  margin: 0;
}

/* Mobile Page Content */
.mobile-page-content {
  flex: 1;
}

/* Mobile Tab Content */
.mobile-tab-content {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.mobile-tab-header {
  flex-shrink: 0;
  padding: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(0, 0, 0, 0.2);
}

.mobile-tab-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
  margin: 0;
}

.mobile-tab-body {
  flex: 1;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

/* Dark mode adjustments for mobile */
.dark .mobile-page-header {
  border-bottom-color: rgba(255, 255, 255, 0.1);
}

.dark .mobile-tab-header {
  border-bottom-color: rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
}
</style>
