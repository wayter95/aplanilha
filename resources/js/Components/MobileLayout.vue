<template>
  <div class="mobile-layout">
    <!-- Mobile Header -->
    <header class="mobile-header">
      <nav class="mobile-nav">
        <!-- Left side: Logo + Toggle button -->
        <div class="mobile-left-side">
          <!-- Logo -->
          <div class="mobile-logo">
            <a href="/" aria-label="Home">
              <img :src="logoIcon" alt="Aplanilha" class="mobile-logo-img" />
            </a>
          </div>
          
          <!-- Toggle button -->
          <button
            class="mobile-toggle-btn"
            @click="toggleSidebar"
            :aria-label="isSidebarOpen ? 'Fechar sidebar' : 'Abrir sidebar'"
          >
            <i v-if="!isSidebarOpen" class="bx bx-menu"></i>
            <i v-else class="bx bx-x"></i>
          </button>
        </div>

        <!-- Right side actions -->
        <div class="mobile-actions">
          <!-- Theme toggle -->
          <button 
            @click="toggleTheme"
            :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
            class="mobile-action-btn"
          >
            <i v-if="isDark" class="bx bx-sun"></i>
            <i v-else class="bx bx-moon"></i>
          </button>

          <!-- User Menu -->
          <div class="mobile-user-menu" ref="userMenuRef">
            <button 
              @click="toggleUserMenu"
              class="mobile-user-btn"
            >
              <div class="mobile-user-avatar">
                <img 
                  v-if="userPhotoUrl" 
                  :src="userPhotoUrl" 
                  :alt="user?.name"
                  class="w-full h-full object-cover rounded-full"
                />
                <span v-else class="text-white font-semibold">
                  {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                </span>
              </div>
            </button>
            
            <div 
              v-show="showUserMenu"
              class="mobile-user-dropdown"
            >
              <ul class="mobile-dropdown-list">
                <li>
                  <a href="/settings" class="mobile-dropdown-item">
                    <i class="bx bx-cog"></i>
                    <span>Configurações</span>
                  </a>
                </li>
                <li>
                  <button @click="logout" class="mobile-dropdown-item">
                    <i class="bx bx-log-out"></i>
                    <span>Sair</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div 
      v-show="isSidebarOpen"
      class="mobile-sidebar-overlay"
      @click="closeSidebar"
    ></div>

    <!-- Mobile Sidebar -->
    <aside 
      :class="[
        'mobile-sidebar',
        { 'mobile-sidebar-open': isSidebarOpen }
      ]"
    >
      <div class="mobile-sidebar-content">
        <ul class="mobile-menu">
          <li 
            v-for="(item, index) in menuItems" 
            :key="index"
            class="mobile-menu-item"
          >
            <!-- Item simples -->
            <template v-if="!item.children">
              <Link 
                :href="item.route"
                class="mobile-menu-link"
                @click="closeSidebar"
              >
                <i :class="[item.icon, 'mobile-menu-icon']"></i>
                <span class="mobile-menu-label">{{ item.label }}</span>
              </Link>
            </template>

            <!-- Item com submenu -->
            <template v-else>
              <button 
                class="mobile-menu-toggle"
                @click="toggleSubmenu(index)"
              >
                <i :class="[item.icon, 'mobile-menu-icon']"></i>
                <span class="mobile-menu-label">{{ item.label }}</span>
                <i 
                  class="bx mobile-menu-arrow"
                  :class="isSubmenuOpen(index) ? 'bx-chevron-up' : 'bx-chevron-down'"
                ></i>
              </button>

              <transition name="mobile-submenu">
                <ul 
                  v-show="isSubmenuOpen(index)"
                  class="mobile-submenu"
                >
                  <li 
                    v-for="(child, cIndex) in item.children"
                    :key="cIndex"
                  >
                    <Link 
                      :href="child.route"
                      class="mobile-submenu-link"
                      @click="closeSidebar"
                    >
                      <i :class="[child.icon, 'mobile-submenu-icon']"></i>
                      <span class="mobile-submenu-label">{{ child.label }}</span>
                    </Link>
                  </li>
                </ul>
              </transition>
            </template>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Mobile Content -->
    <main class="mobile-content">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import logoIcon from '../../assets/images/brand-logos/logo-icon.png'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  },
  menuItems: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['sidebar-toggle'])

const { getPhotoUrl } = usePhotoUrl()

const isSidebarOpen = ref(false)
const openSubmenus = ref([])
const showUserMenu = ref(false)
const userMenuRef = ref(null)
const userPhotoUrl = ref(null)
const isDark = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
  emit('sidebar-toggle', isSidebarOpen.value)
}

const closeSidebar = () => {
  isSidebarOpen.value = false
  emit('sidebar-toggle', false)
}

const toggleSubmenu = (index) => {
  const label = props.menuItems[index]?.label
  if (!label) return

  if (openSubmenus.value.includes(label)) {
    openSubmenus.value = openSubmenus.value.filter(item => item !== label)
  } else {
    openSubmenus.value = [label]
  }

  localStorage.setItem('mobile-sidebar-submenus', JSON.stringify(openSubmenus.value))
}

const isSubmenuOpen = (index) => {
  const label = props.menuItems[index]?.label
  return openSubmenus.value.includes(label)
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const toggleTheme = () => {
  const html = document.documentElement
  isDark.value = !isDark.value
  
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

const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  const savedTheme = localStorage.getItem('app-theme')
  if (savedTheme === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }

  const savedSubmenus = localStorage.getItem('mobile-sidebar-submenus')
  if (savedSubmenus) {
    try {
      const restored = JSON.parse(savedSubmenus)
      if (Array.isArray(restored)) {
        openSubmenus.value = restored
      }
    } catch {
      openSubmenus.value = []
    }
  }

  loadUserPhoto()

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Mobile Layout Container */
.mobile-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

/* Mobile Header */
.mobile-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 3.75rem;
  background: #111827;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  z-index: 1000;
}

.mobile-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  padding: 0 1rem;
}

.mobile-left-side {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.mobile-toggle-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  background: transparent;
  color: #d1d5db;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.mobile-toggle-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.mobile-toggle-btn i {
  font-size: 1.5rem;
}

.mobile-logo {
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-logo-img {
  height: 1.75rem;
  width: 1.75rem;
  object-fit: contain;
}

.mobile-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.mobile-action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  background: transparent;
  color: #d1d5db;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.mobile-action-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.mobile-action-btn i {
  font-size: 1.25rem;
}

/* Mobile User Menu */
.mobile-user-menu {
  position: relative;
}

.mobile-user-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  background: transparent;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.mobile-user-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.mobile-user-avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background-color: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  overflow: hidden;
}

.mobile-user-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  min-width: 12rem;
  background: #1f2937;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
  z-index: 1001;
  overflow: hidden;
}

.mobile-dropdown-list {
  list-style: none;
  margin: 0;
  padding: 0.5rem 0;
}

.mobile-dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  color: #d1d5db;
  text-decoration: none;
  border: none;
  background: transparent;
  transition: all 0.2s;
  text-align: left;
}

.mobile-dropdown-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.mobile-dropdown-item i {
  font-size: 1.125rem;
}

/* Mobile Sidebar Overlay */
.mobile-sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1100;
  backdrop-filter: blur(2px);
}

/* Mobile Sidebar */
.mobile-sidebar {
  position: fixed;
  top: 3.75rem;
  left: -280px;
  width: 280px;
  height: calc(100vh - 3.75rem);
  background: #0f172a;
  transition: left 0.3s ease;
  z-index: 1200;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.mobile-sidebar-open {
  left: 0;
  box-shadow: 8px 0 32px rgba(0, 0, 0, 0.4);
}

.mobile-sidebar-content {
  padding: 1rem 0;
}

/* Mobile Menu */
.mobile-menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

.mobile-menu-item {
  margin: 0;
}

.mobile-menu-link,
.mobile-menu-toggle {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 1rem 1.5rem;
  color: #d1d5db;
  text-decoration: none;
  border: none;
  background: transparent;
  transition: all 0.2s;
  text-align: left;
  border-left: 3px solid transparent;
}

.mobile-menu-link:hover,
.mobile-menu-toggle:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border-left-color: #8b5cf6;
}

.mobile-menu-icon {
  font-size: 1.25rem;
  margin-right: 1rem;
  flex-shrink: 0;
}

.mobile-menu-label {
  flex: 1;
  font-weight: 500;
}

.mobile-menu-arrow {
  font-size: 1rem;
  margin-left: auto;
  transition: transform 0.3s ease;
}

/* Mobile Submenu */
.mobile-submenu {
  list-style: none;
  margin: 0;
  padding: 0;
  background: rgba(0, 0, 0, 0.2);
  border-left: 2px solid rgba(139, 92, 246, 0.3);
  margin-left: 1.5rem;
}

.mobile-submenu-link {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.875rem 1.5rem;
  color: #9ca3af;
  text-decoration: none;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.mobile-submenu-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.mobile-submenu-icon {
  font-size: 1rem;
  margin-right: 1rem;
  flex-shrink: 0;
}

.mobile-submenu-label {
  font-weight: 400;
}

/* Mobile Submenu Transitions */
.mobile-submenu-enter-active,
.mobile-submenu-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.mobile-submenu-enter-from,
.mobile-submenu-leave-to {
  max-height: 0;
  opacity: 0;
}

.mobile-submenu-enter-to,
.mobile-submenu-leave-from {
  max-height: 300px;
  opacity: 1;
}

/* Mobile Content */
.mobile-content {
  flex: 1;
  padding-top: 3.75rem;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

/* Dark mode adjustments */
.dark .mobile-header {
  background: #1a1a1a;
}

.dark .mobile-sidebar {
  background: #1a1a1a;
}

.dark .mobile-user-dropdown {
  background: #2d2d2d;
}
</style>
