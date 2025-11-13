<template>
  <header class="app-header">
    <nav class="main-header !h-[3.75rem]" aria-label="Global">
      <div class="main-header-container ps-[0.725rem] pe-[1rem]">

        <div class="header-content-left">
          <!-- Start::header-element -->
          <div class="header-element">
            <div class="horizontal-logo">
              <Link href="/" class="header-logo">
                <img :src="desktopLogo" alt="logo" class="desktop-logo">
                <img :src="toggleLogo" alt="logo" class="toggle-logo">
                <img :src="desktopDark" alt="logo" class="desktop-dark">
                <img :src="toggleDark" alt="logo" class="toggle-dark">
                <img :src="desktopWhite" alt="logo" class="desktop-white">
                <img :src="toggleWhite" alt="logo" class="toggle-white">
              </Link>
            </div>
          </div>
          <!-- End::header-element -->
          <!-- Start::header-element -->
          <div class="header-element md:px-[0.325rem] !items-center">
            <!-- Start::header-link -->
            <a 
              aria-label="Hide Sidebar"
              class="sidemenu-toggle animated-arrow hor-toggle horizontal-navtoggle inline-flex items-centers" 
              href="javascript:void(0);"
              @click="toggleSidebar"
            >
              <span></span>
            </a>
            <!-- End::header-link -->
          </div>
          <!-- End::header-element -->
        </div>

        <div class="header-content-right">
          
          <!-- Start::header-element -->
          <div class="header-element header-theme-mode hidden !items-center sm:block !py-[1rem] md:!px-[0.65rem] px-2">
            <a 
              v-if="!isDark"
              aria-label="Toggle theme to dark" 
              class="flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 rounded-full font-medium transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10" 
              href="javascript:void(0);"
              @click="toggleTheme"
            >
              <i class="bx bx-moon header-link-icon"></i>
            </a>
            <a 
              v-else
              aria-label="Toggle theme to light" 
              class="flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 rounded-full font-medium text-defaulttextcolor transition-all text-xs dark:bg-bodybg dark:bg-bgdark dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10" 
              href="javascript:void(0);"
              @click="toggleTheme"
            >
              <i class="bx bx-sun header-link-icon"></i>
            </a>
          </div>
          <!-- End::header-element -->

          <!-- Start::header-element -->
          <div class="header-element header-fullscreen py-[1rem] md:px-[0.65rem] px-2">
            <a 
              aria-label="Toggle full screen" 
              class="inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
              href="javascript:void(0);"
              @click="toggleFullscreen"
            >
              <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
              <i class="bx bx-exit-fullscreen full-screen-close header-link-icon hidden"></i>
            </a>
          </div>
          <!-- End::header-element -->

          <!-- Start::header-element - Theme Switcher -->
          <div class="header-element md:!px-[0.65rem] px-2">
            <button
              type="button"
              aria-label="Theme Switcher"
              class="inline-flex flex-shrink-0 justify-center items-center gap-2 !rounded-full font-medium dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
              data-hs-overlay="#hs-overlay-switcher"
            >
              <i class="bx bx-cog header-link-icon animate-spin-slow"></i>
            </button>
          </div>
          <!-- End::header-element -->

          <!-- Start::header-element -->
          <div class="header-element md:!px-[0.65rem] px-2 hs-dropdown !items-center ti-dropdown [--placement:bottom-left]">
            <button 
              id="dropdown-profile" 
              type="button"
              class="hs-dropdown-toggle ti-dropdown-toggle !gap-2 !p-0 flex-shrink-0 sm:me-2 me-0 !rounded-full !shadow-none text-xs align-middle !border-0 !shadow-transparent"
            >
              <img 
                v-if="userPhotoUrl"
                class="inline-block rounded-full" 
                :src="userPhotoUrl" 
                width="32" 
                height="32" 
                :alt="user?.name"
              >
              <span 
                v-else
                class="inline-flex rounded-full items-center justify-center bg-primary text-white w-8 h-8"
              >
                {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </span>
              <div class="md:block hidden dropdown-profile">
                <p class="font-semibold mb-0 leading-none text-[#536485] dark:text-white text-[0.813rem]">
                  {{ displayName }}
                </p>
                <span class="opacity-[0.7] font-normal text-[#536485] dark:text-white/70 block text-[0.6875rem]">
                  {{ user?.email }}
                </span>
              </div>
            </button>
            <div 
              class="hs-dropdown-menu ti-dropdown-menu !-mt-3 border-0 !w-[11rem] !p-0 border-defaultborder hidden main-header-dropdown pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
              aria-labelledby="dropdown-profile"
            >
              <ul class="text-defaulttextcolor font-medium dark:text-[#8c9097] dark:text-white/50">
                <li>
                  <Link class="w-full ti-dropdown-item !text-[0.8125rem] !gap-x-0  !p-[0.65rem] !inline-flex" href="/settings">
                    <i class="ti ti-user-circle text-[1.125rem] me-2 opacity-[0.7]"></i>Profile
                  </Link>
                </li>
                <li>
                  <a class="w-full ti-dropdown-item !text-[0.8125rem] !p-[0.65rem] !gap-x-0 !inline-flex" href="javascript:void(0);" @click="logout">
                    <i class="ti ti-logout text-[1.125rem] me-2 opacity-[0.7]"></i>Log Out
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!-- End::header-element -->

        </div>

      </div>
    </nav>
  </header>
</template>

<script setup>
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { useSidebarToggle } from '@/composables/useSidebarToggle'
import { useFullscreen } from '@/composables/useFullscreen'
import { useTheme } from '@/composables/useTheme'
import { initOverlayTriggers } from '@/composables/useOverlay'
import { router, Link } from '@inertiajs/vue3'
import { onMounted, ref, watch, computed } from 'vue'
import desktopLogo from '../../assets/images/brand-logos/desktop-logo.png'
import toggleLogo from '../../assets/images/brand-logos/toggle-logo.png'
import desktopDark from '../../assets/images/brand-logos/desktop-dark.png'
import toggleDark from '../../assets/images/brand-logos/toggle-dark.png'
import desktopWhite from '../../assets/images/brand-logos/desktop-white.png'
import toggleWhite from '../../assets/images/brand-logos/toggle-white.png'

const props = defineProps({
  user: { type: Object, default: () => ({}) }
})

defineEmits(['toggle-sidebar'])

// Composables
const { toggleSidebar } = useSidebarToggle()
const { toggleFullscreen } = useFullscreen()
const { isDark, toggleTheme } = useTheme()

// User photo
const { getPhotoUrl } = usePhotoUrl()
const userPhotoUrl = ref(null)

const displayName = computed(() => {
  if (!props.user?.name) return 'Usuário'
  const parts = props.user.name.trim().split(' ')
  return parts.length <= 2 ? props.user.name : `${parts[0]} ${parts[1]}`
})

const loadUserPhoto = async () => {
  if (props.user?.photo_key) {
    const url = await getPhotoUrl(props.user.photo_key)
    if (url) userPhotoUrl.value = url
  }
}

watch(() => props.user, loadUserPhoto, { immediate: true })

// Logout
const logout = () => {
  router.post('/logout')
}

onMounted(() => {
  loadUserPhoto()
  initOverlayTriggers()
})
</script>
