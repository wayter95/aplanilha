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
        <div class="container-fluid">
          <slot />
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
import { onMounted } from 'vue'
import { useSidebarToggle } from '@/composables/useSidebarToggle'
import { useRouterEvents } from '@/composables/useRouterEvents'
import { getHtml, getElement, removeAttr, setAttr, removeClass } from '@/utils/dom'

// Props
defineProps({
  title: String,
  description: String,
})

// Sidebar
const { initializeSidebar, restoreSidebarState, toggleSidebar } = useSidebarToggle()

onMounted(() => {
  initializeSidebar()
})

// Router events
const { onStart, onNavigate } = useRouterEvents()

onStart(() => {
  const html = getHtml()
  removeAttr(html, 'data-icon-overlay')
})

onNavigate(() => {
  const html = getHtml()
  
  // Fechar overlay mobile
  const responsiveOverlay = getElement('#responsive-overlay')
  if (responsiveOverlay) {
    removeClass(responsiveOverlay, 'active')
  }
  
  // Fechar sidebar mobile
  if (html && window.innerWidth < 992) {
    setAttr(html, 'data-toggled', 'close')
  }
  
  // Restaurar estado da sidebar no desktop
  if (window.innerWidth >= 992) {
    restoreSidebarState()
  }
})

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
      { label: 'Tipos de Projetos', route: '/project-types' }
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
  
  // { category: 'DOCUMENTOS' },
  // { label: 'Meus Documentos', icon: 'bx bx-file', route: '/documents' },
  // {
  //   label: 'Templates',
  //   icon: 'bx bx-folder',
  //   children: [
  //     { label: 'Modelos de Documentos', route: '/document-templates' },
  //     { label: 'Criar Template', route: '/document-templates/create' }
  //   ]
  // },
  
  // { category: 'RELATÓRIOS' },
  // {
  //   label: 'Relatórios',
  //   icon: 'bx bx-bar-chart',
  //   children: [
  //     { label: 'Dashboard Financeiro', route: '/reports/financial' },
  //     { label: 'Projetos', route: '/reports/projects' },
  //     { label: 'Vendas', route: '/reports/sales' },
  //     { label: 'Usuários', route: '/reports/users' }
  //   ]
  // },
  // { label: 'Logs do Sistema', icon: 'bx bx-history', route: '/logs' }
]
</script>

<style scoped>
/* Layout usando CSS do template Ynex */
</style>
