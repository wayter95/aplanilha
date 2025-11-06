<template>
  <aside class="app-sidebar sticky" id="sidebar">
    
    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
      <Link href="/" class="header-logo">
        <img :src="desktopLogo" alt="logo" class="desktop-logo">
        <img :src="toggleLogo" alt="logo" class="toggle-logo">
        <img :src="desktopDark" alt="logo" class="desktop-dark">
        <img :src="toggleDark" alt="logo" class="toggle-dark">
        <img :src="desktopWhite" alt="logo" class="desktop-white">
        <img :src="toggleWhite" alt="logo" class="toggle-white">
      </Link>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">
      
      <!-- Start::nav -->
      <nav class="main-menu-container nav nav-pills flex-column sub-open">
        <div class="slide-left" id="slide-left">
          <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
          </svg>
        </div>
        
        <ul class="main-menu">
          
          <template v-for="(item, index) in menuItems" :key="index">
            
            <!-- Category -->
            <li v-if="item.category" class="slide__category">
              <span class="category-name">{{ item.category }}</span>
            </li>
            
            <!-- Single Item -->
            <li v-else-if="!item.children" class="slide">
              <Link :href="item.route" class="side-menu__item">
                <i :class="['side-menu__icon', item.icon]"></i>
                <span class="side-menu__label">{{ item.label }}</span>
              </Link>
            </li>
            
            <!-- Item with Children -->
            <li v-else class="slide has-sub">
              <a href="javascript:void(0);" class="side-menu__item">
                <i :class="['side-menu__icon', item.icon]"></i>
                <span class="side-menu__label">{{ item.label }}</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
              </a>
              <ul class="slide-menu child1">
                <li class="slide side-menu__label1">
                  <a href="javascript:void(0)">{{ item.label }}</a>
                </li>
                <li v-for="(child, childIndex) in item.children" :key="childIndex" class="slide">
                  <Link :href="child.route" class="side-menu__item">
                    {{ child.label }}
                  </Link>
                </li>
              </ul>
            </li>
            
          </template>
          
        </ul>
        
        <div class="slide-right" id="slide-right">
          <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
          </svg>
        </div>
      </nav>
      <!-- End::nav -->
      
    </div>
    <!-- End::main-sidebar -->
    
  </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import desktopLogo from '../../assets/images/brand-logos/desktop-logo.png'
import toggleLogo from '../../assets/images/brand-logos/toggle-logo.png'
import desktopDark from '../../assets/images/brand-logos/desktop-dark.png'
import toggleDark from '../../assets/images/brand-logos/toggle-dark.png'
import desktopWhite from '../../assets/images/brand-logos/desktop-white.png'
import toggleWhite from '../../assets/images/brand-logos/toggle-white.png'

defineProps({
  menuItems: { type: Array, default: () => [] }
})
</script>

