<template>
  <aside 
    :class="[
      'app-sidebar',
      { 
        'sidebar-collapsed': isCollapsed && !isHovered,
        'sidebar-hovered': isCollapsed && isHovered,
        'sidebar-open': !isCollapsed
      }
    ]"
    id="sidebar"
  >
    <div class="main-sidebar-header">
      <a href="/" class="header-logo" @click="emitLinkClick">
        <img src="/resources/assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
      </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
      <nav class="main-menu-container nav nav-pills flex-column sub-open">
        <ul class="main-menu">
          <li class="slide">
            <a href="/" class="side-menu__item" @click="emitLinkClick">
              <i class="bx bx-home side-menu__icon"></i>
              <span class="side-menu__label">Home</span>
            </a>
          </li>
          <li class="slide">
            <a href="/users" class="side-menu__item" @click="emitLinkClick">
              <i class="bx bx-user side-menu__icon"></i>
              <span class="side-menu__label">Usuários</span>
            </a>
          </li>
          <li class="slide">
            <a href="/roles" class="side-menu__item" @click="emitLinkClick">
              <i class="bx bx-shield side-menu__icon"></i>
              <span class="side-menu__label">Funções</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script setup>
const props = defineProps({
  isCollapsed: { type: Boolean, default: true },
  isHovered: { type: Boolean, default: false }
})

const emit = defineEmits(['link-click'])
const emitLinkClick = () => emit('link-click')
</script>

<style scoped>
.app-sidebar {
  position: fixed;
  z-index: 999;
  top: 0;
  left: 0;
  height: 100%;
  background: #1f2937;
  transition: width 0.3s ease;
  overflow-x: hidden;
  overflow-y: auto;
  color: #fff;
}

.app-sidebar.sidebar-collapsed {
  width: 72px;
}

.app-sidebar.sidebar-hovered {
  width: 250px;
}

.app-sidebar.sidebar-open {
  width: 250px;
}

.side-menu__item {  
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
}

.side-menu__icon {
  font-size: 1.5rem;
  margin-right: 1rem;
  transition: margin 0.3s;
}

.sidebar-collapsed .side-menu__label {
  display: none;
}

.sidebar-hovered .side-menu__label,
.sidebar-open .side-menu__label {

  display: inline;
}

.sidebar-collapsed .side-menu__icon {
  margin-right: 0;
}
</style>
