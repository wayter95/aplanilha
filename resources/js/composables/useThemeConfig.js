/**
 * Composable para gerenciar configurações de tema do Ynex
 * Aplica configurações do localStorage nos atributos HTML
 * Substitui o main.js do template original
 */

// Função síncrona executada ANTES do Vue montar
export function applyThemeConfigSync() {
  console.log('[useThemeConfig] Aplicando configuracoes de tema')
  const html = document.querySelector('html')
  if (!html) return
  
  // Dark theme
  if (localStorage.getItem('ynexdarktheme')) {
    html.setAttribute('class', 'dark')
    html.setAttribute('data-menu-styles', 'dark')
    html.setAttribute('data-header-styles', 'dark')
  }
  
  // RTL
  if (localStorage.ynexrtl) {
    html.setAttribute('dir', 'rtl')
  }
  
  // Layout horizontal
  if (localStorage.ynexlayout) {
    html.setAttribute('data-nav-layout', 'horizontal')
    html.setAttribute('data-menu-styles', 'light')
    html.setAttribute('data-nav-style', 'menu-click')
  }
  
  // Loader
  if (localStorage.loaderEnable === 'true') {
    html.setAttribute('loader', 'enable')
  } else {
    if (!html.getAttribute('loader')) {
      html.setAttribute('loader', 'disable')
    }
  }
  
  // Sidebar toggled state
  // Se tem algo salvo, aplica. Se não, mantém o padrão do HTML (icon-overlay-close)
  const savedToggled = localStorage.getItem('ynextoggledSidebar')
  console.log('[useThemeConfig] Estado sidebar no localStorage:', savedToggled)
  
  if (savedToggled !== null) {
    if (savedToggled) {
      html.setAttribute('data-toggled', savedToggled)
      console.log('[useThemeConfig] Sidebar fechada - data-toggled:', savedToggled)
    } else {
      html.removeAttribute('data-toggled')
      console.log('[useThemeConfig] Sidebar aberta - data-toggled removido')
    }
  } else {
    console.log('[useThemeConfig] Nenhum estado salvo - mantendo padrao HTML')
  }
  // Se não tem nada salvo, mantém o que está no HTML (data-toggled="icon-overlay-close")
  
  // Primary color RGB
  if (localStorage.primaryRGB) {
    html.style.setProperty('--primary', localStorage.primaryRGB1)
    html.style.setProperty('--primary-rgb', localStorage.primaryRGB)
  }
  
  // Body background RGB
  if (localStorage.bodyBgRGB) {
    html.style.setProperty('--body-bg', localStorage.bodyBgRGB)
    html.style.setProperty('--dark-bg', localStorage.darkBgRGB)
    html.style.setProperty('--light', localStorage.darkBgRGB)
    html.classList.add('dark')
    html.classList.remove('light')
    html.setAttribute('data-menu-styles', 'dark')
    html.setAttribute('data-header-styles', 'dark')
  }
  
  // Vertical styles (com todas as variações)
  if (localStorage.ynexverticalstyles) {
    const verticalStyles = localStorage.getItem('ynexverticalstyles')
    html.setAttribute('data-vertical-style', verticalStyles)
    
    // Se é overlay, setar como fechado (minimizado) por padrão
    if (verticalStyles === 'overlay') {
      html.setAttribute('data-toggled', 'icon-overlay-close')
    }
    
    localStorage.removeItem('ynexnavstyles')
    
    // Doublemenu precisa criar tooltips
    if (verticalStyles === 'doublemenu') {
      setTimeout(() => {
        const menuSlideItem = document.querySelectorAll('.main-menu > li > .side-menu__item')
        if (menuSlideItem.length === 0) return
        
        const tooltip = document.createElement('div')
        tooltip.className = 'custome-tooltip'
        tooltip.style.setProperty('position', 'fixed')
        tooltip.style.setProperty('display', 'none')
        tooltip.style.setProperty('padding', '0.5rem')
        tooltip.style.setProperty('font-weight', '500')
        tooltip.style.setProperty('font-size', '0.75rem')
        tooltip.style.setProperty('background-color', 'rgb(15, 23, 42)')
        tooltip.style.setProperty('color', 'rgb(255, 255, 255)')
        tooltip.style.setProperty('margin-inline-start', '45px')
        tooltip.style.setProperty('border-radius', '0.25rem')
        tooltip.style.setProperty('z-index', '99')
        
        menuSlideItem.forEach((e) => {
          e.addEventListener('mouseenter', () => {
            tooltip.style.setProperty('display', 'block')
            const label = e.querySelector('.side-menu__label')
            tooltip.textContent = label ? label.textContent : ''
            if (document.querySelector('html').getAttribute('data-vertical-style') === 'doublemenu') {
              e.appendChild(tooltip)
            }
          })
          
          e.addEventListener('mouseleave', () => {
            tooltip.style.setProperty('display', 'none')
          })
        })
      }, 1000)
    }
  }
  
  // Nav styles
  if (localStorage.ynexnavstyles) {
    const navStyles = localStorage.getItem('ynexnavstyles')
    html.setAttribute('data-nav-style', navStyles)
    localStorage.removeItem('ynexverticalstyles')
    html.removeAttribute('data-vertical-style')
  }
  
  // Page style
  if (localStorage.ynexclassic) {
    html.setAttribute('data-page-style', 'classic')
  }
  if (localStorage.ynexmodern) {
    html.setAttribute('data-page-style', 'modern')
  }
  
  // Width
  if (localStorage.ynexboxed) {
    html.setAttribute('data-width', 'boxed')
  }
  
  // Header position
  if (localStorage.ynexheaderfixed) {
    html.setAttribute('data-header-position', 'fixed')
  }
  if (localStorage.ynexheaderscrollable) {
    html.setAttribute('data-header-position', 'scrollable')
  }
  
  // Menu position
  if (localStorage.ynexmenufixed) {
    html.setAttribute('data-menu-position', 'fixed')
  }
  if (localStorage.ynexmenuscrollable) {
    html.setAttribute('data-menu-position', 'scrollable')
  }
  
  // Menu styles
  if (localStorage.ynexMenu) {
    const menuValue = localStorage.getItem('ynexMenu')
    html.setAttribute('data-menu-styles', menuValue)
  }
  
  // Header styles
  if (localStorage.ynexHeader) {
    const headerValue = localStorage.getItem('ynexHeader')
    html.setAttribute('data-header-styles', headerValue)
  }
  
  // Background image
  if (localStorage.bgimg) {
    const value = localStorage.getItem('bgimg')
    html.setAttribute('bg-img', value)
  }
}

// Composable para uso no Vue (opcional, já que executamos síncronamente no app.js)
export function useThemeConfig() {
  // Apenas retorna a função, já foi executada no app.js
  return {
    applyThemeConfig: applyThemeConfigSync
  }
}
