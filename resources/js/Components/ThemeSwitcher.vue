<template>
  <div id="hs-overlay-switcher" class="hs-overlay hidden ti-offcanvas ti-offcanvas-right" tabindex="-1">
    <div class="ti-offcanvas-header z-10 relative">
      <h5 class="ti-offcanvas-title">Switcher</h5>
      <button 
        type="button"
        class="ti-btn flex-shrink-0 p-0 transition-none text-defaulttextcolor dark:text-defaulttextcolor/70 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
        @click="close"
      >
        <span class="sr-only">Close modal</span>
        <i class="ri-close-circle-line leading-none text-lg"></i>
      </button>
    </div>

    <div class="ti-offcanvas-body !p-0 !border-b dark:border-white/10 z-10 relative !h-auto">
      <div class="flex rtl:space-x-reverse" aria-label="Tabs" role="tablist">
        <button 
          type="button"
          :class="activeTab === 'styles' ? 'hs-tab-active:bg-success/20 hs-tab-active:text-success active' : ''"
          class="w-full !py-2 !px-4 text-defaultsize border-0 -mb-px bg-white font-semibold text-center text-defaulttextcolor dark:text-defaulttextcolor/70 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10"
          @click="activeTab = 'styles'"
        >
          Theme Style
        </button>
        <button 
          type="button"
          :class="activeTab === 'colors' ? 'hs-tab-active:bg-success/20 hs-tab-active:text-success active' : ''"
          class="w-full !py-2 !px-4 text-defaultsize border-0 -mb-px bg-white font-semibold text-center text-defaulttextcolor dark:text-defaulttextcolor/70 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10"
          @click="activeTab = 'colors'"
        >
          Theme Colors
        </button>
      </div>
    </div>

    <div class="ti-offcanvas-body" id="switcher-body">
      <!-- Tab 1: Theme Styles -->
      <div v-show="activeTab === 'styles'">
        <!-- Theme Color Mode -->
        <div class="">
          <p class="switcher-style-head">Theme Color Mode:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex items-center">
              <input 
                type="radio" 
                name="theme-style" 
                class="ti-form-radio" 
                id="switcher-light-theme"
                value="light"
                v-model="themeMode"
                @change="setThemeMode('light')"
              >
              <label for="switcher-light-theme" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Light
              </label>
            </div>
            <div class="flex items-center">
              <input 
                type="radio" 
                name="theme-style" 
                class="ti-form-radio" 
                id="switcher-dark-theme"
                value="dark"
                v-model="themeMode"
                @change="setThemeMode('dark')"
              >
              <label for="switcher-dark-theme" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Dark
              </label>
            </div>
          </div>
        </div>

        <!-- Directions -->
        <div>
          <p class="switcher-style-head">Directions:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex items-center">
              <input 
                type="radio" 
                name="direction" 
                class="ti-form-radio" 
                id="switcher-ltr"
                value="ltr"
                v-model="direction"
                @change="setDirection('ltr')"
              >
              <label for="switcher-ltr" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                LTR
              </label>
            </div>
            <div class="flex items-center">
              <input 
                type="radio" 
                name="direction" 
                class="ti-form-radio" 
                id="switcher-rtl"
                value="rtl"
                v-model="direction"
                @change="setDirection('rtl')"
              >
              <label for="switcher-rtl" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                RTL
              </label>
            </div>
          </div>
        </div>

        <!-- Navigation Styles -->
        <div>
          <p class="switcher-style-head">Navigation Styles:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex items-center">
              <input 
                type="radio" 
                name="navigation-style" 
                class="ti-form-radio" 
                id="switcher-vertical"
                value="vertical"
                v-model="navigationStyle"
                @change="setNavigationStyle('vertical')"
              >
              <label for="switcher-vertical" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Vertical
              </label>
            </div>
            <div class="flex items-center">
              <input 
                type="radio" 
                name="navigation-style" 
                class="ti-form-radio" 
                id="switcher-horizontal"
                value="horizontal"
                v-model="navigationStyle"
                @change="setNavigationStyle('horizontal')"
              >
              <label for="switcher-horizontal" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Horizontal
              </label>
            </div>
          </div>
        </div>

        <!-- Page Styles -->
        <div>
          <p class="switcher-style-head">Page Styles:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex">
              <input 
                type="radio" 
                name="data-page-styles" 
                class="ti-form-radio" 
                id="switcher-regular"
                value="regular"
                v-model="pageStyle"
                @change="setPageStyle('regular')"
              >
              <label for="switcher-regular" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Regular
              </label>
            </div>
            <div class="flex">
              <input 
                type="radio" 
                name="data-page-styles" 
                class="ti-form-radio" 
                id="switcher-classic"
                value="classic"
                v-model="pageStyle"
                @change="setPageStyle('classic')"
              >
              <label for="switcher-classic" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Classic
              </label>
            </div>
            <div class="flex">
              <input 
                type="radio" 
                name="data-page-styles" 
                class="ti-form-radio" 
                id="switcher-modern"
                value="modern"
                v-model="pageStyle"
                @change="setPageStyle('modern')"
              >
              <label for="switcher-modern" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Modern
              </label>
            </div>
          </div>
        </div>

        <!-- Layout Width -->
        <div>
          <p class="switcher-style-head">Layout Width Styles:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex">
              <input 
                type="radio" 
                name="layout-width" 
                class="ti-form-radio" 
                id="switcher-full-width"
                value="fullwidth"
                v-model="layoutWidth"
                @change="setLayoutWidth('fullwidth')"
              >
              <label for="switcher-full-width" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                FullWidth
              </label>
            </div>
            <div class="flex">
              <input 
                type="radio" 
                name="layout-width" 
                class="ti-form-radio" 
                id="switcher-boxed"
                value="boxed"
                v-model="layoutWidth"
                @change="setLayoutWidth('boxed')"
              >
              <label for="switcher-boxed" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Boxed
              </label>
            </div>
          </div>
        </div>

        <!-- Loader -->
        <div class="">
          <p class="switcher-style-head">Loader:</p>
          <div class="grid grid-cols-3 switcher-style">
            <div class="flex">
              <input 
                type="radio" 
                name="page-loader" 
                class="ti-form-radio" 
                id="switcher-loader-enable"
                value="enable"
                v-model="loaderStatus"
                @change="setLoader('enable')"
              >
              <label for="switcher-loader-enable" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Enable
              </label>
            </div>
            <div class="flex">
              <input 
                type="radio" 
                name="page-loader" 
                class="ti-form-radio" 
                id="switcher-loader-disable"
                value="disable"
                v-model="loaderStatus"
                @change="setLoader('disable')"
              >
              <label for="switcher-loader-disable" class="text-defaultsize text-defaulttextcolor dark:text-defaulttextcolor/70 ms-2 font-semibold">
                Disable
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Theme Colors -->
      <div v-show="activeTab === 'colors'">
        <!-- Menu Colors -->
        <div class="theme-colors">
          <p class="switcher-style-head">Menu Colors:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div 
              v-for="color in menuColors" 
              :key="color.id"
              class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
            >
              <input 
                class="hs-tooltip-toggle ti-form-radio color-input"
                :class="`color-${color.class}`"
                type="radio" 
                name="menu-colors"
                :id="color.id"
                :value="color.value"
                v-model="menuColor"
                @change="setMenuColor(color.value)"
              >
              <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black">
                {{ color.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Header Colors -->
        <div class="theme-colors">
          <p class="switcher-style-head">Header Colors:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div 
              v-for="color in headerColors" 
              :key="color.id"
              class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
            >
              <input 
                class="hs-tooltip-toggle ti-form-radio color-input"
                :class="[`color-${color.class}`, color.class === 'white' ? '!border' : '']"
                type="radio" 
                name="header-colors"
                :id="color.id"
                :value="color.value"
                v-model="headerColor"
                @change="setHeaderColor(color.value)"
              >
              <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black">
                {{ color.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Theme Primary -->
        <div class="theme-colors">
          <p class="switcher-style-head">Theme Primary:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div 
              v-for="(color, index) in primaryColors" 
              :key="color.id"
              class="ti-form-radio switch-select"
            >
              <input 
                class="ti-form-radio color-input"
                :class="`color-primary-${index + 1}`"
                type="radio" 
                name="theme-primary"
                :id="color.id"
                :value="color.value"
                v-model="primaryColor"
                @change="setPrimaryColor(color.value, color.rgb)"
              >
            </div>
          </div>
        </div>

        <!-- Theme Background -->
        <div class="theme-colors">
          <p class="switcher-style-head">Theme Background:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div 
              v-for="(color, index) in backgroundColors" 
              :key="color.id"
              class="ti-form-radio switch-select"
            >
              <input 
                class="ti-form-radio color-input"
                :class="`color-bg-${index + 1}`"
                type="radio" 
                name="theme-background"
                :id="color.id"
                :value="color.value"
                v-model="backgroundColor"
                @change="setBackgroundColor(color.value, color.rgb, color.dark)"
              >
            </div>
          </div>
        </div>

        <!-- Menu Background Images -->
        <div class="menu-image theme-colors">
          <p class="switcher-style-head">Menu With Background Image:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse flex-wrap gap-3">
            <div 
              v-for="(img, index) in backgroundImages" 
              :key="img.id"
              class="ti-form-radio switch-select"
            >
              <input 
                class="ti-form-radio bgimage-input"
                :class="`bg-img${index + 1}`"
                type="radio" 
                name="theme-images"
                :id="img.id"
                :value="img.value"
                v-model="backgroundImage"
                @change="setBackgroundImage(img.value)"
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="ti-offcanvas-footer sm:flex justify-between">
      <button 
        type="button"
        class="w-full ti-btn btn-wave ti-btn-danger-full m-1"
        @click="resetAll"
      >
        Reset
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeSwitcher } from '@/composables/useThemeSwitcher'
import { useOverlay } from '@/composables/useOverlay'
import { useHtmlElement } from '@/composables/useHtmlElement'
import { useOverlayEvents } from '@/composables/useEvents'

// Inicializar HTML element helper
const { html, getAttribute, hasClass } = useHtmlElement()

// Inicializar overlay (substitui Preline)
const { isOpen, open, close, toggle } = useOverlay('hs-overlay-switcher')

// Escutar evento global de abertura do overlay usando composable
const { onToggle } = useOverlayEvents()
onToggle((event) => {
  if (event.detail.overlayId === 'hs-overlay-switcher') {
    toggle()
  }
})

const {
  setThemeMode,
  setDirection,
  setNavigationStyle,
  setPageStyle,
  setLayoutWidth,
  setLoader,
  setMenuColor,
  setHeaderColor,
  setPrimaryColor,
  setBackgroundColor,
  setBackgroundImage,
  resetAll
} = useThemeSwitcher()

const activeTab = ref('styles')

// Estados reativos
const themeMode = ref('light')
const direction = ref('ltr')
const navigationStyle = ref('vertical')
const pageStyle = ref('regular')
const layoutWidth = ref('fullwidth')
const loaderStatus = ref('enable')
const menuColor = ref('dark')
const headerColor = ref('light')
const primaryColor = ref('primary1')
const backgroundColor = ref('bg1')
const backgroundImage = ref('')

// Dados de cores
const menuColors = [
  { id: 'switcher-menu-light', value: 'light', label: 'Light Menu', class: 'white' },
  { id: 'switcher-menu-dark', value: 'dark', label: 'Dark Menu', class: 'dark' },
  { id: 'switcher-menu-primary', value: 'color', label: 'Color Menu', class: 'primary' },
  { id: 'switcher-menu-gradient', value: 'gradient', label: 'Gradient Menu', class: 'gradient' },
  { id: 'switcher-menu-transparent', value: 'transparent', label: 'Transparent Menu', class: 'transparent' }
]

const headerColors = [
  { id: 'switcher-header-light', value: 'light', label: 'Light Header', class: 'white' },
  { id: 'switcher-header-dark', value: 'dark', label: 'Dark Header', class: 'dark' },
  { id: 'switcher-header-primary', value: 'color', label: 'Color Header', class: 'primary' },
  { id: 'switcher-header-gradient', value: 'gradient', label: 'Gradient Header', class: 'gradient' },
  { id: 'switcher-header-transparent', value: 'transparent', label: 'Transparent Header', class: 'transparent' }
]

const primaryColors = [
  { id: 'switcher-primary', value: 'primary1', rgb: '58, 88, 146', rgb1: '58 88 146' },
  { id: 'switcher-primary1', value: 'primary2', rgb: '92, 144, 163', rgb1: '92 144 163' },
  { id: 'switcher-primary2', value: 'primary3', rgb: '161, 90, 223', rgb1: '161 90 223' },
  { id: 'switcher-primary3', value: 'primary4', rgb: '78, 172, 76', rgb1: '78 172 76' },
  { id: 'switcher-primary4', value: 'primary5', rgb: '223, 90, 90', rgb1: '223 90 90' }
]

const backgroundColors = [
  { id: 'switcher-background', value: 'bg1', rgb: '34, 44, 110', dark: '20 30 96' },
  { id: 'switcher-background1', value: 'bg2', rgb: '22, 92, 129', dark: '8 78 115' },
  { id: 'switcher-background2', value: 'bg3', rgb: '104, 51, 149', dark: '90 37 135' },
  { id: 'switcher-background3', value: 'bg4', rgb: '22, 92, 129', dark: '8 78 115' },
  { id: 'switcher-background4', value: 'bg5', rgb: '104, 51, 149', dark: '90 37 135' }
]

const backgroundImages = [
  { id: 'switcher-bg-img', value: 'bgimg1' },
  { id: 'switcher-bg-img1', value: 'bgimg2' },
  { id: 'switcher-bg-img2', value: 'bgimg3' },
  { id: 'switcher-bg-img3', value: 'bgimg4' },
  { id: 'switcher-bg-img4', value: 'bgimg5' }
]

// Carregar estados do localStorage ao montar
onMounted(() => {
  // Usar useHtmlElement em vez de document.querySelector
  if (!html.value) return
  
  // Ler estados do HTML usando helpers
  themeMode.value = hasClass('dark') ? 'dark' : 'light'
  direction.value = getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr'
  navigationStyle.value = getAttribute('data-nav-layout') === 'horizontal' ? 'horizontal' : 'vertical'
  
  const pageStyleAttr = getAttribute('data-page-style')
  pageStyle.value = pageStyleAttr || 'regular'
  
  const widthAttr = getAttribute('data-width')
  layoutWidth.value = widthAttr === 'boxed' ? 'boxed' : 'fullwidth'
  
  const loaderAttr = getAttribute('loader')
  loaderStatus.value = loaderAttr === 'enable' ? 'enable' : 'disable'
  
  const menuStyleAttr = getAttribute('data-menu-styles')
  menuColor.value = menuStyleAttr || 'dark'
  
  const headerStyleAttr = getAttribute('data-header-styles')
  headerColor.value = headerStyleAttr || 'light'
})
</script>
