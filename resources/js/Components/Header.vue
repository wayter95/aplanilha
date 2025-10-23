<template>
  <header class="app-header">
    <nav class="main-header !h-[3.75rem]" aria-label="Global">
      <div class="main-header-container ps-[0.725rem] pe-[1rem]">
        
        <div class="header-content-left">
          <div class="header-element">
            <div class="horizontal-logo">
              <a href="/" class="header-logo">
                <h2 class="text-2xl font-bold text-white">Aplanilha</h2>
              </a>
            </div>
          </div>
          
          <div class="header-element md:px-[0.325rem] !items-center">
            <a 
              aria-label="Hide Sidebar"
              class="sidemenu-toggle animated-arrow hor-toggle horizontal-navtoggle inline-flex items-center" 
              href="javascript:void(0);"
              @click="toggleSidebarCollapse"
            >
              <span></span>
            </a>
          </div>
        </div>

        <div class="header-content-right">

          <div class="header-element header-theme-mode hidden !items-center sm:block !py-[1rem] md:!px-[0.65rem] px-2">
            <button 
              v-if="isLight"
              @click="toggleTheme"
              aria-label="Switch to dark theme"
              class="header-link-icon inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10 hover:bg-gray-100 p-2"
            >
              <i class="bx bx-moon text-lg"></i>
            </button>
            <button 
              v-if="isDark"
              @click="toggleTheme"
              aria-label="Switch to light theme"
              class="header-link-icon inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10 hover:bg-gray-100 p-2"
            >
              <i class="bx bx-sun text-lg"></i>
            </button>
          </div>

          <div class="header-element py-[1rem] md:px-[0.65rem] px-2 notifications-dropdown header-notification !hidden md:!block">
            <button 
              @click="toggleNotifications"
              type="button"
              class="header-link-icon relative inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10 hover:bg-gray-100 p-2"
            >
              <i class="bx bx-bell text-lg"></i>
              <span class="flex absolute h-5 w-5 -top-[0.25rem] end-0 -me-[0.6rem]">
                <span class="animate-slow-ping absolute inline-flex -top-[2px] -start-[2px] h-full w-full rounded-full bg-secondary/40 opacity-75"></span>
                <span class="relative inline-flex justify-center items-center rounded-full h-[14.7px] w-[14px] bg-secondary text-[0.625rem] text-white">3</span>
              </span>
            </button>
            
            <div 
              v-show="showNotifications"
              class="absolute right-0 top-full mt-2 bg-white dark:bg-gray-800 !w-[22rem] border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 !m-0"
            >
              <div class="ti-dropdown-header !m-0 !p-4 !bg-transparent flex justify-between items-center">
                <p class="mb-0 text-[1.0625rem] text-defaulttextcolor font-semibold dark:text-white/50">Notificações</p>
                <span class="text-[0.75em] py-[0.25rem/2] px-[0.45rem] font-[600] rounded-sm bg-secondary/10 text-secondary" id="notifiation-data">3 Não Lidas</span>
              </div>
              <div class="dropdown-divider"></div>
              
              <ul class="list-none !m-0 !p-0 end-0" id="header-notification-scroll">
                <li class="ti-dropdown-item dropdown-item !block">
                  <div class="flex items-start">
                    <div class="pe-2">
                      <span class="inline-flex text-primary justify-center items-center !w-[2.5rem] !h-[2.5rem] !leading-[2.5rem] !text-[0.8rem] !bg-primary/10 !rounded-[50%]">
                        <i class="bx bx-user-check text-[1.125rem]"></i>
                      </span>
                    </div>
                    <div class="grow flex items-center justify-between">
                      <div>
                        <p class="mb-0 text-defaulttextcolor dark:text-white text-[0.8125rem] font-semibold">
                          <a href="/notifications">Novo Usuário Cadastrado</a>
                        </p>
                        <span class="text-[#8c9097] dark:text-white/50 font-normal text-[0.75rem] header-notification-text">
                          Um novo usuário foi cadastrado no sistema
                        </span>
                      </div>
                      <div>
                        <a aria-label="anchor" href="javascript:void(0);" class="min-w-fit text-[#8c9097] dark:text-white/50 me-1 dropdown-item-close1">
                          <i class="bx bx-x text-[1rem]"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
                
                <li class="ti-dropdown-item dropdown-item !block">
                  <div class="flex items-start">
                    <div class="pe-2">
                      <span class="inline-flex text-success justify-center items-center !w-[2.5rem] !h-[2.5rem] !leading-[2.5rem] !text-[0.8rem] bg-success/10 rounded-[50%]">
                        <i class="bx bx-check-circle text-[1.125rem]"></i>
                      </span>
                    </div>
                    <div class="grow flex items-center justify-between">
                      <div>
                        <p class="mb-0 text-defaulttextcolor dark:text-white text-[0.8125rem] font-semibold">
                          <a href="/notifications">Sistema Atualizado</a>
                        </p>
                        <span class="text-[#8c9097] dark:text-white/50 font-normal text-[0.75rem] header-notification-text">
                          Sistema foi atualizado com sucesso
                        </span>
                      </div>
                      <div>
                        <a aria-label="anchor" href="javascript:void(0);" class="min-w-fit text-[#8c9097] dark:text-white/50 me-1 dropdown-item-close1">
                          <i class="bx bx-x text-[1rem]"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
                
                <li class="ti-dropdown-item dropdown-item !block">
                  <div class="flex items-start">
                    <div class="pe-2">
                      <span class="inline-flex text-warning justify-center items-center !w-[2.5rem] !h-[2.5rem] !leading-[2.5rem] !text-[0.8rem] bg-warning/10 rounded-[50%]">
                        <i class="bx bx-info-circle text-[1.125rem]"></i>
                      </span>
                    </div>
                    <div class="grow flex items-center justify-between">
                      <div>
                        <p class="mb-0 text-defaulttextcolor dark:text-white text-[0.8125rem] font-semibold">
                          <a href="/notifications">Manutenção Programada</a>
                        </p>
                        <span class="text-[#8c9097] dark:text-white/50 font-normal text-[0.75rem] header-notification-text">
                          Sistema em manutenção às 02:00
                        </span>
                      </div>
                      <div>
                        <a aria-label="anchor" href="javascript:void(0);" class="min-w-fit text-[#8c9097] dark:text-white/50 me-1 dropdown-item-close1">
                          <i class="bx bx-x text-[1rem]"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>

              <div class="p-4 empty-header-item1 border-t mt-2">
                <div class="grid">
                  <a href="/notifications" class="ti-btn ti-btn-primary-full !m-0 w-full p-2">Ver Todas</a>
                </div>
              </div>
            </div>
          </div>

          <div class="header-element header-fullscreen py-[1rem] md:px-[0.65rem] px-2">
            <button 
              @click="toggleFullscreen"
              aria-label="Toggle fullscreen" 
              class="header-link-icon inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10 hover:bg-gray-100 p-2"
            >
              <i v-if="!isFullscreen" class="bx bx-fullscreen text-lg"></i>
              <i v-else class="bx bx-exit-fullscreen text-lg"></i>
            </button>
          </div>

          <div class="header-element md:!px-[0.65rem] px-2 !items-center relative user-dropdown">
            <button 
              @click="toggleUserMenu"
              type="button"
              class="header-link-icon inline-flex flex-shrink-0 justify-center items-center gap-3 !rounded-full font-medium dark:hover:bg-black/20 dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10 hover:bg-gray-100 p-2"
            >
              <div class="user-avatar">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
            </button>
            
            <div class="md:block hidden dropdown-profile">
              <p class="font-semibold mb-0 leading-none text-[#536485] dark:text-white text-[0.813rem]">{{ getDisplayName(user?.name) }}</p>
              <span class="opacity-[0.7] font-normal text-[#536485] dark:text-white/70 block text-[0.6875rem]">{{ user?.email || 'email@exemplo.com' }}</span>
            </div>
            
            <div 
              v-show="showUserMenu"
              class="absolute right-0 top-full mt-2 border-0 w-[11rem] !p-0 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 pt-0 overflow-hidden header-profile-dropdown"
            >
              <ul class="text-defaulttextcolor font-medium dark:text-white/50">
                <li>
                  <a class="w-full ti-dropdown-item !text-[0.8125rem] !gap-x-0 !p-[0.65rem] !inline-flex" href="/profile">
                    <i class="bx bx-user-circle text-[1.125rem] me-2 opacity-[0.7]"></i>Perfil
                  </a>
                </li>
                <li>
                  <a class="w-full ti-dropdown-item !text-[0.8125rem] !gap-x-0 !p-[0.65rem] !inline-flex" href="/settings">
                    <i class="bx bx-cog text-[1.125rem] me-2 opacity-[0.7]"></i>Configurações
                  </a>
                </li>
                <li>
                  <button @click="logout" class="w-full ti-dropdown-item !text-[0.8125rem] !p-[0.65rem] !gap-x-0 !inline-flex text-left">
                    <i class="bx bx-log-out text-[1.125rem] me-2 opacity-[0.7]"></i>Sair
                  </button>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['toggle-sidebar'])

const showNotifications = ref(false)
const showUserMenu = ref(false)
const isFullscreen = ref(false)
const isDark = ref(false)
const isLight = ref(false)

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) {
    showUserMenu.value = false
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  if (showUserMenu.value) {
    showNotifications.value = false
  }
}

const toggleFullscreen = () => {
  if (typeof document === 'undefined') return
  
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen?.()
  } else {
    document.exitFullscreen?.()
  }
  isFullscreen.value = !isFullscreen.value
}

const toggleTheme = () => {
  if (typeof document === 'undefined') return
  
  const html = document.documentElement
  isDark.value = !isDark.value
  isLight.value = !isDark.value
  
  if (isDark.value) {
    html.classList.add('dark')
  } else {
    html.classList.remove('dark')
  }
  
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('app-theme', isDark.value ? 'dark' : 'light')
  }
}

const toggleSidebarCollapse = () => {
  if (typeof document === 'undefined') return
  
  const htmlElement = document.documentElement
  const isCollapsed = htmlElement.classList.contains('sidebar-collapsed')
  
  if (isCollapsed) {
    htmlElement.classList.remove('sidebar-collapsed')
  } else {
    htmlElement.classList.add('sidebar-collapsed')
  }
  
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('sidebar-collapsed', (!isCollapsed).toString())
  }
}

const logout = () => {
  router.post('/logout')
}

const getDisplayName = (fullName) => {
  if (!fullName) return 'Usuário'
  
  const nameParts = fullName.trim().split(' ')
  if (nameParts.length <= 2) {
    return fullName
  }
  
  return `${nameParts[0]} ${nameParts[1]}`
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.header-profile-dropdown') && !event.target.closest('.header-link-icon')) {
    showUserMenu.value = false
    showNotifications.value = false
  }
}

onMounted(() => {
  if (typeof localStorage !== 'undefined' && typeof document !== 'undefined') {
    const savedTheme = localStorage.getItem('app-theme')
    if (savedTheme === 'dark') {
      isDark.value = true
      isLight.value = false
      document.documentElement.classList.add('dark')
    } else {
      isDark.value = false
      isLight.value = true
    }
    
      isFullscreen.value = !!document.fullscreenElement
    
    const savedSidebarState = localStorage.getItem('sidebar-collapsed')
    if (savedSidebarState === 'true') {
      document.documentElement.classList.add('sidebar-collapsed')
    }
  }
  
  document.addEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.header-link-icon {
  transition: all 0.15s ease-in-out;
}

.header-link-icon:hover {
  transform: translateY(-1px);
}

.header-link-icon:active {
  transform: translateY(0);
}

.user-avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background-color: var(--primary-color, #3b82f6);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  line-height: 1;
  overflow: hidden;
  flex-shrink: 0;
}

.dropdown-profile {
  margin-left: 0.75rem;
}

.user-dropdown {
  display: flex;
  align-items: center;
}

.dropdown-divider {
  height: 1px;
  background: rgb(229, 231, 235);
  margin: 0.5rem 0;
}

@media (max-width: 768px) {
  .header-center {
    display: none;
  }
  
  .d-sm-block {
    display: none !important;
  }
}
</style>
