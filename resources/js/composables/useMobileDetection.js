import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Composable para detectar dispositivos móveis de forma reativa
 */
export function useMobileDetection() {
  const isMobile = ref(false)
  const screenWidth = ref(0)
  
  // Breakpoints para diferentes tamanhos de tela
  const breakpoints = {
    mobile: 768,     // tablets em modo portrait e menores
    tablet: 992,     // tablets em modo landscape
    desktop: 1200    // desktops
  }

  const checkMobile = () => {
    screenWidth.value = window.innerWidth
    isMobile.value = window.innerWidth <= breakpoints.mobile
  }

  const isTablet = () => {
    return window.innerWidth > breakpoints.mobile && window.innerWidth <= breakpoints.tablet
  }

  const isDesktop = () => {
    return window.innerWidth > breakpoints.tablet
  }

  const isTouchDevice = () => {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0
  }

  const getUserAgent = () => {
    return navigator.userAgent
  }

  const isMobileUserAgent = () => {
    const userAgent = getUserAgent().toLowerCase()
    return /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent)
  }

  // Detecção mais robusta que combina largura da tela e user agent
  const isMobileDevice = () => {
    return isMobile.value || isMobileUserAgent() || (isTouchDevice() && window.innerWidth <= breakpoints.tablet)
  }

  onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
  })

  return {
    isMobile,
    screenWidth,
    breakpoints,
    isTablet,
    isDesktop,
    isTouchDevice,
    isMobileUserAgent,
    isMobileDevice
  }
}