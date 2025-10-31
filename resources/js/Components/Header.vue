<template>
  <header class="app-header">
    <nav class="main-header !h-[3.75rem]" aria-label="Global">
      <div class="main-header-container ps-[0.725rem] pe-[1rem]" :style="{ paddingLeft: boxWidth }">
        <!-- Header side box that matches the sidebar width; contains clickable logo -->
        <div class="header-sidebox" :style="{ width: boxWidth }">
          <a href="/" class="header-sidebox-logo" aria-label="Home">
            <img :src="currentLogo" alt="Aplanilha" class="header-sidebox-logo-img" />
          </a>
        </div>
        <!-- Toggle button placed to the right of the header-sidebox -->
        <button
          class="header-sidebox-toggle"
          @click="toggleSidebar"
          :style="{ left: buttonLeft }"
          :aria-label="(isSidebarCollapsed && !isSidebarHovered) ? 'Abrir sidebar' : 'Fechar sidebar'"
        >
          <i v-if="isSidebarCollapsed && !isSidebarHovered" class="bx bx-menu"></i>
          <i v-else class="bx bx-x"></i>
        </button>
        
        <div class="header-content-left">
          <div class="header-element">
            <div class="horizontal-logo">
              <a href="/" class="header-logo">
              </a>
            </div>
          </div>
        </div>

        <div class="header-content-right">

          <!-- Theme toggle -->
          <div class="header-element header-theme-mode hidden !items-center sm:block !py-[1rem] md:!px-[0.65rem] px-2">
            <button 
              v-if="isLight"
              @click="toggleTheme"
              aria-label="Switch to dark theme"
              class="header-link-icon p-2"
            >
              <i class="bx bx-moon text-lg"></i>
            </button>
            <button 
              v-if="isDark"
              @click="toggleTheme"
              aria-label="Switch to light theme"
              class="header-link-icon p-2"
            >
              <i class="bx bx-sun text-lg"></i>
            </button>
          </div>

          <!-- Fullscreen -->
          <div class="header-element header-fullscreen py-[1rem] md:px-[0.65rem] px-2">
            <button 
              @click="toggleFullscreen"
              aria-label="Toggle fullscreen" 
              class="header-link-icon p-2"
            >
              <i v-if="!isFullscreen" class="bx bx-fullscreen text-lg"></i>
              <i v-else class="bx bx-exit-fullscreen text-lg"></i>
            </button>
          </div>

          <!-- User Menu -->
          <div class="header-element md:!px-[0.65rem] px-2 !items-center relative user-dropdown">
            <button 
              @click="toggleUserMenu"
              type="button"
              class="header-link-icon inline-flex flex-shrink-0 justify-center items-center gap-3 !rounded-full font-medium p-2"
            >
              <div class="user-avatar">
                <img 
                  v-if="userPhotoUrl" 
                  :src="userPhotoUrl" 
                  :alt="user?.name"
                  class="w-full h-full object-cover"
                  style="border-radius: 50%;"
                />
                <span v-else class="text-white font-semibold">
                  {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                </span>
              </div>
            </button>
            
            <div class="md:block hidden dropdown-profile">
              <p class="font-semibold mb-0 leading-none text-[#536485] dark:text-white text-[0.813rem]">{{ getDisplayName(user?.name) }}</p>
              <span class="opacity-[0.7] font-normal text-[#536485] dark:text-white/70 block text-[0.6875rem]">{{ user?.email || 'email@exemplo.com' }}</span>
            </div>
            
            <div 
              v-show="showUserMenu"
              class="absolute right-0 top-full mt-2 border-0 w-[11rem] !p-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 pt-0 overflow-hidden header-profile-dropdown"
            >
              <ul class="text-defaulttextcolor font-medium dark:text-white/50">
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
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { router } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref, watch, computed } from 'vue'
import logoFull from '../../assets/images/brand-logos/logo-full.png'
import logoIcon from '../../assets/images/brand-logos/logo-icon.png'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  },
  isSidebarCollapsed: { type: Boolean, default: true },
  isSidebarHovered: { type: Boolean, default: false }
})

const emit = defineEmits(['toggle-sidebar'])

const { getPhotoUrl } = usePhotoUrl()

const showUserMenu = ref(false)
const isFullscreen = ref(false)
const userPhotoUrl = ref(null)
const isDark = ref(false)
const isLight = ref(false)

// compute header box width to match sidebar (collapsed:72px, expanded:250px)
const boxWidth = computed(() => {
  const collapsedAndNotHovered = props.isSidebarCollapsed && !props.isSidebarHovered
  return collapsedAndNotHovered ? '72px' : '250px'
})

// choose logo image depending on sidebar state
const currentLogo = computed(() => {
  const collapsedAndNotHovered = props.isSidebarCollapsed && !props.isSidebarHovered
  return collapsedAndNotHovered ? logoIcon : logoFull
})

// position for toggle button (8px gap to the right of box)
const buttonLeft = computed(() => `calc(${boxWidth.value} + 8px)`)

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const toggleSidebar = () => {
  emit('toggle-sidebar')
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen?.()
  } else {
    document.exitFullscreen?.()
  }
  isFullscreen.value = !isFullscreen.value
}

const toggleTheme = () => {
  const html = document.documentElement
  isDark.value = !isDark.value
  isLight.value = !isDark.value
  
  if (isDark.value) {
    html.classList.add('dark')
  } else {
    html.classList.remove('dark')
  }
  
  localStorage.setItem('app-theme', isDark.value ? 'dark' : 'light')
}

const logout = () => {
  router.post('/logout')
}

const loadUserPhoto = async () => {
  if (props.user?.photo_key) {
    const url = await getPhotoUrl(props.user.photo_key)
    if (url) userPhotoUrl.value = url
  } else {
    userPhotoUrl.value = null
  }
}

watch(() => props.user, () => {
  loadUserPhoto()
}, { immediate: true })

onMounted(() => {
  const savedTheme = localStorage.getItem('app-theme')
  if (savedTheme === 'dark') {
    isDark.value = true
    isLight.value = false
    document.documentElement.classList.add('dark')
  } else {
    isDark.value = false
    isLight.value = true
  }

  loadUserPhoto()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const getDisplayName = (fullName) => {
  if (!fullName) return 'Usuário'
  const parts = fullName.trim().split(' ')
  return parts.length <= 2 ? fullName : `${parts[0]} ${parts[1]}`
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.header-profile-dropdown') && !event.target.closest('.header-link-icon')) {
    showUserMenu.value = false
  }
}
</script>

<style scoped>
/* Estilos do Header */
.header-link-icon {
  transition: all 0.15s ease-in-out;
}

.header-link-icon:hover {
  transform: translateY(-1px);
}

/* Avatar do usuário */
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

/* Header sidebox that mirrors the sidebar width */
.header-sidebox {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  height: 3.75rem; /* same as header */
  /* match Sidebar.vue background */
  background: #111C43;
  border-right: 1px solid rgba(255,255,255,0.05);
  transition: width 0.25s ease;
  z-index: 1001; /* below header content but above page background */
}

/* ensure same appearance in dark mode (when html has class 'dark') */
.dark .header-sidebox {
  background: #1A1C1E;
  border-right-color: rgba(255,255,255,0.05);
}

.header-sidebox-logo { display: flex; align-items: center; justify-content: center; height: 100%; }
.header-sidebox-logo-img { max-height: 36px; object-fit: contain; transition: opacity 0.15s ease, transform 0.15s ease; margin: 0 auto; }

.main-header-container {
  transition: padding-left 0.25s ease;
}

/* Toggle button placed right of the header-sidebox (absolute) */
.header-sidebox-toggle{
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  background: transparent;
  border: none;
  color: #9aa0a6;
  z-index: 1004; /* above header-sidebox */
  transition: left 0.25s ease, background 0.15s ease, color 0.15s ease;
}
.header-sidebox-toggle:hover{ background: rgba(255,255,255,0.04); color: #fff }

/* Make the toggle icon match sidebar icons */
.header-sidebox-toggle i {
  /* Match collapsed sidebar icon size */
  font-size: 1.5rem;
  color: #d1d5db;
}
.header-sidebox-toggle:hover i {
  color: #fff;
}
</style>
