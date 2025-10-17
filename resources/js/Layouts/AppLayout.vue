<template>
  <div class="page">
    <!-- HEADER -->
    <header class="app-header">
      <nav class="main-header !h-[3.75rem]" aria-label="Global">
        <div class="main-header-container ps-[0.725rem] pe-[1rem]">
          <div class="header-content-left">
            <div class="header-element">
              <div class="horizontal-logo">
                <a href="/" class="header-logo">
                  <h2 class="text-2xl font-bold text-primary">Aplanilha</h2>
                </a>
              </div>
            </div>
            <div class="header-element md:px-[0.325rem] !items-center">
              <a 
                @click="toggleSidebar"
                class="sidemenu-toggle animated-arrow hor-toggle horizontal-navtoggle inline-flex items-center" 
                href="javascript:void(0);"
              >
                <span></span>
              </a>
            </div>
          </div>

          <div class="header-content-right">
            <div class="header-element py-[1rem] md:px-[0.65rem] px-2 header-search">
              <button 
                type="button" 
                class="inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-full font-medium focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
              >
                <i class="bx bx-search-alt-2 header-link-icon"></i>
              </button>
            </div>

            <div class="header-element py-[1rem] md:px-[0.65rem] px-2">
              <div class="hs-dropdown ti-dropdown relative">
                <button 
                  @click="toggleUserMenu"
                  type="button"
                  class="hs-dropdown-toggle ti-dropdown-toggle !p-0 flex-shrink-0 !border-0 !rounded-full !shadow-none"
                >
                  <div class="h-[2rem] w-[2rem] rounded-full bg-primary flex items-center justify-center text-white font-semibold">
                    {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                  </div>
                </button>
                
                <div 
                  :class="['hs-dropdown-menu ti-dropdown-menu min-w-[12rem] !-mt-3', { 'hidden': !showUserMenu }]"
                  aria-labelledby="dropdown-user"
                >
                  <div class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10">
                    <div class="py-2 first:pt-0 last:pb-0">
                      <div class="ti-dropdown-item !p-[0.65rem]">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse w-full">
                          <div class="h-[2rem] flex items-center w-[2rem] rounded-full">
                            <div class="h-[2rem] w-[2rem] rounded-full bg-primary flex items-center justify-center text-white font-semibold text-sm">
                              {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                            </div>
                          </div>
                          <div>
                            <p class="!text-[0.8125rem] font-medium text-defaulttextcolor">{{ user?.name }}</p>
                            <p class="!text-[0.75rem] text-textmuted">{{ user?.email }}</p>
                          </div>
                        </div>
                      </div>
                      <div class="ti-dropdown-item !p-[0.65rem]">
                        <a href="/profile" class="flex items-center space-x-2 rtl:space-x-reverse w-full text-defaulttextcolor">
                          <i class="ri-user-line text-[1rem]"></i>
                          <span class="!text-[0.8125rem]">Meu Perfil</span>
                        </a>
                      </div>
                      <div class="ti-dropdown-item !p-[0.65rem]">
                        <a href="/settings" class="flex items-center space-x-2 rtl:space-x-reverse w-full text-defaulttextcolor">
                          <i class="ri-settings-3-line text-[1rem]"></i>
                          <span class="!text-[0.8125rem]">Configurações</span>
                        </a>
                      </div>
                      <div class="ti-dropdown-divider"></div>
                      <div class="ti-dropdown-item !p-[0.65rem]">
                        <button 
                          @click="logout"
                          class="flex items-center space-x-2 rtl:space-x-reverse w-full text-danger"
                        >
                          <i class="ri-logout-box-line text-[1rem]"></i>
                          <span class="!text-[0.8125rem]">Sair</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <!-- SIDEBAR -->
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
              <a href="/reports" class="side-menu__item" :class="{ 'active': $page.url.startsWith('/reports') }">
                <i class="bx bx-bar-chart-alt-2 side-menu__icon"></i>
                <span class="side-menu__label">Relatórios</span>
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

    <!-- MAIN-CONTENT -->
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

    <!-- Overlay for mobile -->
    <div 
      v-if="sidebarOpen" 
      @click="closeSidebar"
      class="sidebar-overlay"
    ></div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  title: String,
  description: String,
  user: Object
})

const sidebarOpen = ref(false)
const showUserMenu = ref(false)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebar = () => {
  sidebarOpen.value = false
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const logout = () => {
  router.post('/logout')
}

// Close user menu when clicking outside
const handleClickOutside = (e) => {
  if (!e.target.closest('.ti-dropdown')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
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
  display: none;
}

@media (max-width: 767px) {
  .sidebar-overlay {
    display: block;
  }
}
</style>