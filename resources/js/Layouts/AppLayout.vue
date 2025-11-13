<template>
  <div class="page">
    <!-- Loader -->
    <Loader />
    
    <!-- Header -->
    <Header 
      @toggle-sidebar="toggleSidebar"
    />

    <!-- Toast Container -->
    <ToastContainer position="top-right" />

    <!-- Sidebar -->
    <Sidebar :menu-items="menuItems" />

    <!-- Theme Switcher -->
    <ThemeSwitcher />

    <!-- Conteúdo principal -->
    <div class="content">
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
import Loader from '@/Components/Loader.vue'
import ToastContainer from '@/Components/ToastContainer.vue'
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
import { ref, onMounted, watch, computed, shallowRef } from 'vue'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'
import { storeToRefs } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { useSidebarToggle } from '@/composables/useSidebarToggle'
import { useRouterEvents } from '@/composables/useRouterEvents'
import { getHtml, getElement, removeAttr, setAttr, removeClass } from '@/utils/dom'

const page = usePage()
const tabsStore = useTabsMemoryStore()
const { activeTab, tabs } = storeToRefs(tabsStore)
const hasTabs = computed(() => tabs.value.length > 0)

// Props
defineProps({
  title: String,
  description: String,
})

// Sidebar
const { initializeSidebar, restoreSidebarState } = useSidebarToggle()

onMounted(() => {
  initializeSidebar()
})

// Router events
const { onStart, onNavigate } = useRouterEvents()

onStart(() => {
  console.log('[AppLayout] Navigation START - Removendo overlay temporario')
  const html = getHtml()
  removeAttr(html, 'data-icon-overlay')
  console.log('[AppLayout] data-icon-overlay removido:', html.getAttribute('data-icon-overlay'))
})

onNavigate(() => {
  console.log('[AppLayout] Navigation NAVIGATE')
  const html = getHtml()
  
  // Fechar overlay mobile
  const responsiveOverlay = getElement('#responsive-overlay')
  if (responsiveOverlay) {
    removeClass(responsiveOverlay, 'active')
    console.log('[AppLayout] Responsive overlay fechado')
  }
  
  // Fechar sidebar mobile
  if (html && window.innerWidth < 992) {
    setAttr(html, 'data-toggled', 'close')
    console.log('[AppLayout] Sidebar mobile fechada')
  }
  
  // Restaurar estado da sidebar no desktop
  if (window.innerWidth >= 992) {
    console.log('[AppLayout] Desktop - Restaurando estado da sidebar')
    restoreSidebarState()
  }
})

// Tab system
const shouldShowTabContent = computed(() => {
  if (!activeTab.value || !activeTab.value.path) return false
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
    const { loadTabComponent } = await import('@/config/tabComponents')
    const component = await loadTabComponent(newTab.componentName)
    resolvedComponent.value = component
  } catch (error) {
    resolvedComponent.value = null
  }
}, { immediate: true })

// Menu items
const menuItems = [
  { category: 'PRINCIPAL' },
  { label: 'Dashboard', icon: 'bx bx-home', route: '/' },
  { label: 'Analytics', icon: 'bx bx-line-chart', route: '/analytics' },
  {
    label: 'Projetos',
    icon: 'bx bx-briefcase',
    children: [
      { label: 'Projetos', route: '/projects' },
      { label: 'Tipos de Projetos', route: '/projects/types' }
    ]
  },
  
  { category: 'ADMINISTRAÇÃO' },
  {
    label: 'Usuários',
    icon: 'bx bx-user',
    children: [
      { label: 'Lista de Usuários', route: '/users' },
      { label: 'Novo Usuário', route: '/users/create' },
      { label: 'Funções e Permissões', route: '/roles' }
    ]
  },
  {
    label: 'Configurações',
    icon: 'bx bx-cog',
    children: [
      { label: 'Geral', route: '/settings/general' },
      { label: 'Email', route: '/settings/email' },
      { label: 'Integrações', route: '/settings/integrations' }
    ]
  },
  
  { category: 'DOCUMENTOS' },
  { label: 'Meus Documentos', icon: 'bx bx-file', route: '/documents' },
  {
    label: 'Templates',
    icon: 'bx bx-folder',
    children: [
      { label: 'Modelos de Documentos', route: '/document-templates' },
      { label: 'Criar Template', route: '/document-templates/create' }
    ]
  },
  
  { category: 'RELATÓRIOS' },
  {
    label: 'Relatórios',
    icon: 'bx bx-bar-chart',
    children: [
      { label: 'Dashboard Financeiro', route: '/reports/financial' },
      { label: 'Projetos', route: '/reports/projects' },
      { label: 'Vendas', route: '/reports/sales' },
      { label: 'Usuários', route: '/reports/users' }
    ]
  },
  { label: 'Logs do Sistema', icon: 'bx bx-history', route: '/logs' }
]
</script>

<style scoped>
/* Layout usando CSS do template Ynex */
</style>
