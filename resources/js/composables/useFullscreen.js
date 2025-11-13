/**
 * Composable para gerenciar fullscreen
 * Uso: const { isFullscreen, toggleFullscreen } = useFullscreen()
 */

import { ref } from 'vue'
import { getElement, addClass, removeClass } from '@/utils/dom'

export function useFullscreen() {
  const isFullscreen = ref(false)

  const enterFullscreen = () => {
    const elem = document.documentElement
    
    if (elem.requestFullscreen) {
      elem.requestFullscreen()
    } else if (elem.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen()
    } else if (elem.msRequestFullscreen) {
      elem.msRequestFullscreen()
    }
  }

  const exitFullscreen = () => {
    if (document.exitFullscreen) {
      document.exitFullscreen()
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen()
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen()
    }
  }

  const updateIcons = (entering) => {
    const open = getElement(".full-screen-open")
    const close = getElement(".full-screen-close")
    
    if (!open || !close) return
    
    if (entering) {
      addClass(close, "block")
      removeClass(close, "hidden")
      addClass(open, "hidden")
    } else {
      removeClass(close, "block")
      addClass(close, "hidden")
      removeClass(open, "hidden")
      addClass(open, "block")
    }
  }

  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      enterFullscreen()
      updateIcons(true)
      isFullscreen.value = true
    } else {
      exitFullscreen()
      updateIcons(false)
      isFullscreen.value = false
    }
  }

  // Listener para mudanças de fullscreen (ESC, F11, etc)
  const handleFullscreenChange = () => {
    isFullscreen.value = !!document.fullscreenElement
    updateIcons(isFullscreen.value)
  }

  document.addEventListener('fullscreenchange', handleFullscreenChange)
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange)
  document.addEventListener('msfullscreenchange', handleFullscreenChange)

  return {
    isFullscreen,
    toggleFullscreen,
    enterFullscreen,
    exitFullscreen
  }
}
