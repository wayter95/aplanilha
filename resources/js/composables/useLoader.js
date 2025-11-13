/**
 * Composable para gerenciar o loader da página
 * Substitui a funcionalidade do custom.js do template original
 */

import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

export function useLoader() {
  
  const hideLoader = () => {
    const loader = document.getElementById('loader')
    if (loader) {
      loader.classList.add('d-none')
    }
  }

  const showLoader = () => {
    const loader = document.getElementById('loader')
    if (loader) {
      loader.classList.remove('d-none')
    }
  }

  // Inicializar loader
  const initLoader = () => {
    const html = document.querySelector('html')
    const loaderEnabled = localStorage.getItem('loaderEnable') !== 'false'
    
    if (!loaderEnabled) {
      hideLoader()
      return
    }

    // Esconder loader quando a página terminar de carregar
    if (document.readyState === 'complete') {
      hideLoader()
    } else {
      window.addEventListener('load', hideLoader)
    }
  }

  // Listeners do Inertia para mostrar/esconder loader durante navegação
  const setupInertiaListeners = () => {
    const loaderEnabled = localStorage.getItem('loaderEnable') !== 'false'
    
    if (!loaderEnabled) return

    // Mostrar loader quando começar a navegar
    router.on('start', () => {
      showLoader()
    })

    // Esconder loader quando terminar de carregar
    router.on('finish', () => {
      // Delay pequeno para suavizar a transição
      setTimeout(hideLoader, 100)
    })
  }

  return {
    hideLoader,
    showLoader,
    initLoader,
    setupInertiaListeners
  }
}
