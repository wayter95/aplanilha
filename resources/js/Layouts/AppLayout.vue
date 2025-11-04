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
          'content-collapsed': isSidebarCollapsed && !isSidebarHovered,
          'content-with-tabs': hasTabs
        }
      ]"
    >
      <div class="main-content">
        <div class="container-fluid" :class="{ 'tab-active': shouldShowTabContent }">
          <div v-if="!shouldShowTabContent" class="page-header">
            <div>
              <h1 class="page-title">{{ title }}</h1>
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
import ToastContainer from '@/Components/ToastContainer.vue'
import { ref, onMounted, watch, computed, shallowRef } from 'vue'
import { useTabsStore } from '@/stores/useTabsStore'
import { storeToRefs } from 'pinia'
import { usePage } from '@inertiajs/vue3'

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
  
  if (newTab.componentName === 'DocumentTemplatesForm') {
    const module = await import('@/Pages/DocumentTemplates/Form.vue')
    resolvedComponent.value = module.default
  } else {
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
const isMobile = ref(false)

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

/* toggle button moved into Header.vue */
</style>
