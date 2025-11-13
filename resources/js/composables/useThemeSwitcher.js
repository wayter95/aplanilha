/**
 * Composable completo para gerenciar TODAS as configurações de tema do Ynex
 * Substitui o custom-switcher.js do template original
 * 
 * Funcionalidades:
 * - Dark/Light mode
 * - Primary colors (5 variações)
 * - Background colors (5 variações)
 * - Background images (5 variações)
 * - RTL/LTR
 * - Vertical/Horizontal layout
 * - Navigation styles (menu-click, menu-hover, icon-click, icon-hover)
 * - Vertical styles (default, closed, icontext, overlay, detached, doublemenu)
 * - Page styles (regular, classic, modern)
 * - Width (full-width, boxed)
 * - Menu position (fixed, scrollable)
 * - Header position (fixed, scrollable)
 * - Menu styles (light, dark, color, gradient, transparent)
 * - Header styles (light, dark, color, gradient, transparent)
 * - Loader (enable, disable)
 * - Reset all
 */

import { ref, computed } from 'vue'

export function useThemeSwitcher() {
  const html = document.querySelector('html')
  
  // ==================== THEME MODE (Dark/Light) ====================
  
  const setLightTheme = () => {
    html.classList.remove('dark')
    html.classList.add('light')
    html.setAttribute('data-menu-styles', 'light')
    html.setAttribute('data-header-styles', 'light')
    localStorage.removeItem('ynexdarktheme')
    localStorage.removeItem('bodyBgRGB')
    localStorage.removeItem('darkBgRGB')
    localStorage.removeItem('bgimg')
    html.style.removeProperty('--body-bg')
    html.style.removeProperty('--dark-bg')
    html.style.removeProperty('--light')
    html.removeAttribute('bg-img')
  }
  
  const setDarkTheme = () => {
    html.classList.add('dark')
    html.classList.remove('light')
    html.setAttribute('data-menu-styles', 'dark')
    html.setAttribute('data-header-styles', 'dark')
    localStorage.setItem('ynexdarktheme', 'true')
  }
  
  // ==================== PRIMARY COLORS ====================
  
  const setPrimaryColor = (rgb, rgb1) => {
    localStorage.setItem('primaryRGB', rgb)
    localStorage.setItem('primaryRGB1', rgb1)
    html.style.setProperty('--primary-rgb', rgb)
    html.style.setProperty('--primary', rgb1)
  }
  
  // Cores pré-definidas
  const primaryColors = {
    color1: { rgb: '58, 88, 146', rgb1: '58 88 146' },
    color2: { rgb: '92, 144, 163', rgb1: '92 144 163' },
    color3: { rgb: '161, 90, 223', rgb1: '161 90 223' },
    color4: { rgb: '78, 172, 76', rgb1: '78 172 76' },
    color5: { rgb: '223, 90, 90', rgb1: '223 90 90' }
  }
  
  const setPrimaryColor1 = () => setPrimaryColor(primaryColors.color1.rgb, primaryColors.color1.rgb1)
  const setPrimaryColor2 = () => setPrimaryColor(primaryColors.color2.rgb, primaryColors.color2.rgb1)
  const setPrimaryColor3 = () => setPrimaryColor(primaryColors.color3.rgb, primaryColors.color3.rgb1)
  const setPrimaryColor4 = () => setPrimaryColor(primaryColors.color4.rgb, primaryColors.color4.rgb1)
  const setPrimaryColor5 = () => setPrimaryColor(primaryColors.color5.rgb, primaryColors.color5.rgb1)
  
  // ==================== BACKGROUND COLORS ====================
  
  const setBackgroundColor = (bodyBg, darkBg, light, inputBorder) => {
    localStorage.setItem('bodyBgRGB', bodyBg)
    localStorage.setItem('darkBgRGB', darkBg)
    localStorage.setItem('--light', light)
    localStorage.setItem('ynexMenu', 'dark')
    localStorage.setItem('ynexHeader', 'dark')
    localStorage.removeItem('hs_theme')
    
    html.classList.add('dark')
    html.classList.remove('light')
    html.setAttribute('data-menu-styles', 'dark')
    html.setAttribute('data-header-styles', 'dark')
    html.style.setProperty('--body-bg', bodyBg)
    html.style.setProperty('--dark-bg', darkBg)
    html.style.setProperty('--light', light)
    html.style.setProperty('--input-border', inputBorder)
  }
  
  const backgroundColors = {
    color1: { bodyBg: '34 44 110', darkBg: '20 30 96', light: '25 38 101', inputBorder: '55 67 98' },
    color2: { bodyBg: '22 92 129', darkBg: '8 78 115', light: '13 86 120', inputBorder: '13 86 120' },
    color3: { bodyBg: '104 51 149', darkBg: '90 37 135', light: '95 42 140', inputBorder: '95 42 140' },
    color4: { bodyBg: '38 92 50', darkBg: '24 78 36', light: '29 83 41', inputBorder: '29 83 41' },
    color5: { bodyBg: '100 37 50', darkBg: '86 23 36', light: '91 28 41', inputBorder: '91 28 41' }
  }
  
  const setBackgroundColor1 = () => setBackgroundColor(
    backgroundColors.color1.bodyBg,
    backgroundColors.color1.darkBg,
    backgroundColors.color1.light,
    backgroundColors.color1.inputBorder
  )
  
  const setBackgroundColor2 = () => setBackgroundColor(
    backgroundColors.color2.bodyBg,
    backgroundColors.color2.darkBg,
    backgroundColors.color2.light,
    backgroundColors.color2.inputBorder
  )
  
  const setBackgroundColor3 = () => setBackgroundColor(
    backgroundColors.color3.bodyBg,
    backgroundColors.color3.darkBg,
    backgroundColors.color3.light,
    backgroundColors.color3.inputBorder
  )
  
  const setBackgroundColor4 = () => setBackgroundColor(
    backgroundColors.color4.bodyBg,
    backgroundColors.color4.darkBg,
    backgroundColors.color4.light,
    backgroundColors.color4.inputBorder
  )
  
  const setBackgroundColor5 = () => setBackgroundColor(
    backgroundColors.color5.bodyBg,
    backgroundColors.color5.darkBg,
    backgroundColors.color5.light,
    backgroundColors.color5.inputBorder
  )
  
  // ==================== BACKGROUND IMAGES ====================
  
  const setBackgroundImage = (imageUrl) => {
    localStorage.setItem('bgimg', imageUrl)
    html.setAttribute('bg-img', imageUrl)
  }
  
  const setBackgroundImage1 = () => setBackgroundImage('bgimg1')
  const setBackgroundImage2 = () => setBackgroundImage('bgimg2')
  const setBackgroundImage3 = () => setBackgroundImage('bgimg3')
  const setBackgroundImage4 = () => setBackgroundImage('bgimg4')
  const setBackgroundImage5 = () => setBackgroundImage('bgimg5')
  
  const removeBackgroundImage = () => {
    localStorage.removeItem('bgimg')
    html.removeAttribute('bg-img')
  }
  
  // ==================== RTL/LTR ====================
  
  const setRTL = () => {
    html.setAttribute('dir', 'rtl')
    localStorage.setItem('ynexrtl', 'true')
  }
  
  const setLTR = () => {
    html.setAttribute('dir', 'ltr')
    localStorage.removeItem('ynexrtl')
  }
  
  // ==================== LAYOUT (Vertical/Horizontal) ====================
  
  const setVerticalLayout = () => {
    html.setAttribute('data-nav-layout', 'vertical')
    html.removeAttribute('data-nav-style')
    localStorage.removeItem('ynexlayout')
    localStorage.removeItem('ynexnavstyles')
  }
  
  const setHorizontalLayout = () => {
    html.setAttribute('data-nav-layout', 'horizontal')
    html.setAttribute('data-nav-style', 'menu-click')
    html.setAttribute('data-menu-styles', 'light')
    localStorage.setItem('ynexlayout', 'horizontal')
  }
  
  // ==================== PAGE STYLES ====================
  
  const setRegularPage = () => {
    html.removeAttribute('data-page-style')
    localStorage.removeItem('ynexclassic')
    localStorage.removeItem('ynexmodern')
  }
  
  const setClassicPage = () => {
    html.setAttribute('data-page-style', 'classic')
    localStorage.setItem('ynexclassic', 'true')
    localStorage.removeItem('ynexmodern')
  }
  
  const setModernPage = () => {
    html.setAttribute('data-page-style', 'modern')
    localStorage.setItem('ynexmodern', 'true')
    localStorage.removeItem('ynexclassic')
  }
  
  // ==================== WIDTH ====================
  
  const setFullWidth = () => {
    html.removeAttribute('data-width')
    localStorage.removeItem('ynexboxed')
  }
  
  const setBoxedWidth = () => {
    html.setAttribute('data-width', 'boxed')
    localStorage.setItem('ynexboxed', 'true')
  }
  
  // ==================== MENU POSITION ====================
  
  const setMenuFixed = () => {
    html.setAttribute('data-menu-position', 'fixed')
    localStorage.setItem('ynexmenufixed', 'true')
    localStorage.removeItem('ynexmenuscrollable')
  }
  
  const setMenuScrollable = () => {
    html.setAttribute('data-menu-position', 'scrollable')
    localStorage.setItem('ynexmenuscrollable', 'true')
    localStorage.removeItem('ynexmenufixed')
  }
  
  // ==================== HEADER POSITION ====================
  
  const setHeaderFixed = () => {
    html.setAttribute('data-header-position', 'fixed')
    localStorage.setItem('ynexheaderfixed', 'true')
    localStorage.removeItem('ynexheaderscrollable')
  }
  
  const setHeaderScrollable = () => {
    html.setAttribute('data-header-position', 'scrollable')
    localStorage.setItem('ynexheaderscrollable', 'true')
    localStorage.removeItem('ynexheaderfixed')
  }
  
  // ==================== MENU STYLES ====================
  
  const setMenuStyle = (style) => {
    html.setAttribute('data-menu-styles', style)
    localStorage.setItem('ynexMenu', style)
  }
  
  const setMenuLight = () => setMenuStyle('light')
  const setMenuDark = () => setMenuStyle('dark')
  const setMenuColor = () => setMenuStyle('color')
  const setMenuGradient = () => setMenuStyle('gradient')
  const setMenuTransparent = () => setMenuStyle('transparent')
  
  // ==================== HEADER STYLES ====================
  
  const setHeaderStyle = (style) => {
    html.setAttribute('data-header-styles', style)
    localStorage.setItem('ynexHeader', style)
  }
  
  const setHeaderLight = () => setHeaderStyle('light')
  const setHeaderDark = () => setHeaderStyle('dark')
  const setHeaderColor = () => setHeaderStyle('color')
  const setHeaderGradient = () => setHeaderStyle('gradient')
  const setHeaderTransparent = () => setHeaderStyle('transparent')
  
  // ==================== LOADER ====================
  
  const enableLoader = () => {
    html.setAttribute('loader', 'enable')
    localStorage.setItem('loaderEnable', 'true')
  }
  
  const disableLoader = () => {
    html.setAttribute('loader', 'disable')
    localStorage.setItem('loaderEnable', 'false')
  }
  
  // ==================== RESET ALL ====================
  
  const resetAll = () => {
    // Remove all localStorage items
    const keysToRemove = [
      'ynexdarktheme', 'ynexrtl', 'ynexlayout', 'ynexverticalstyles',
      'ynexnavstyles', 'ynexclassic', 'ynexmodern', 'ynexboxed',
      'ynexheaderfixed', 'ynexheaderscrollable', 'ynexmenufixed',
      'ynexmenuscrollable', 'ynexMenu', 'ynexHeader', 'loaderEnable',
      'primaryRGB', 'primaryRGB1', 'bodyBgRGB', 'darkBgRGB', 'bgimg', '--light'
    ]
    
    keysToRemove.forEach(key => localStorage.removeItem(key))
    
    // Reset HTML attributes to defaults
    html.setAttribute('class', 'light')
    html.setAttribute('dir', 'ltr')
    html.setAttribute('data-nav-layout', 'vertical')
    html.setAttribute('data-menu-styles', 'dark')
    html.setAttribute('data-header-styles', 'light')
    html.setAttribute('loader', 'disable')
    html.removeAttribute('data-vertical-style')
    html.removeAttribute('data-nav-style')
    html.removeAttribute('data-page-style')
    html.removeAttribute('data-width')
    html.removeAttribute('data-menu-position')
    html.removeAttribute('data-header-position')
    html.removeAttribute('bg-img')
    html.removeAttribute('data-toggled')
    
    // Reset CSS variables
    html.style.removeProperty('--primary')
    html.style.removeProperty('--primary-rgb')
    html.style.removeProperty('--body-bg')
    html.style.removeProperty('--dark-bg')
    html.style.removeProperty('--light')
    html.style.removeProperty('--input-border')
    
    // Reload page to apply all defaults
    window.location.reload()
  }
  
  return {
    // Theme mode
    setLightTheme,
    setDarkTheme,
    
    // Primary colors
    setPrimaryColor1,
    setPrimaryColor2,
    setPrimaryColor3,
    setPrimaryColor4,
    setPrimaryColor5,
    
    // Background colors
    setBackgroundColor1,
    setBackgroundColor2,
    setBackgroundColor3,
    setBackgroundColor4,
    setBackgroundColor5,
    
    // Background images
    setBackgroundImage1,
    setBackgroundImage2,
    setBackgroundImage3,
    setBackgroundImage4,
    setBackgroundImage5,
    removeBackgroundImage,
    
    // RTL/LTR
    setRTL,
    setLTR,
    
    // Layout
    setVerticalLayout,
    setHorizontalLayout,
    
    // Page styles
    setRegularPage,
    setClassicPage,
    setModernPage,
    
    // Width
    setFullWidth,
    setBoxedWidth,
    
    // Menu position
    setMenuFixed,
    setMenuScrollable,
    
    // Header position
    setHeaderFixed,
    setHeaderScrollable,
    
    // Menu styles
    setMenuLight,
    setMenuDark,
    setMenuColor,
    setMenuGradient,
    setMenuTransparent,
    
    // Header styles
    setHeaderLight,
    setHeaderDark,
    setHeaderColor,
    setHeaderGradient,
    setHeaderTransparent,
    
    // Loader
    enableLoader,
    disableLoader,
    
    // Reset
    resetAll
  }
}
