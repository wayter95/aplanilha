import { ref } from 'vue'
import { getItem, setItem } from '@/utils/localStorage'
import { getHtml, getElement, setAttr, getAttr, removeAttr } from '@/utils/dom'

export function useSidebarToggle() {
  const isOpen = ref(true)
  
  // Funções para mouseenter/mouseleave no overlay mode
  const mouseEntered = () => {
    const html = getHtml()
    setAttr(html, 'data-icon-overlay', 'open')
    console.log('[useSidebarToggle] Mouse entered - overlay temporario aberto')
  }

  const mouseLeave = () => {
    const html = getHtml()
    removeAttr(html, 'data-icon-overlay')
    console.log('[useSidebarToggle] Mouse leave - overlay temporario fechado')
  }
  
  // Salvar estado no localStorage
  const saveToLocalStorage = (toggled) => {
    // Se toggled é null ou undefined, significa sidebar ABERTA (sem data-toggled)
    // Salvar string vazia para diferenciar de "não tem valor salvo"
    if (toggled) {
      setItem('ynextoggledSidebar', toggled)
    } else {
      // Sidebar aberta = remover data-toggled = salvar string vazia
      setItem('ynextoggledSidebar', '')
    }
  }
  
  // Carregar estado do localStorage
  const loadFromLocalStorage = () => {
    return getItem('ynextoggledSidebar')
  }
  
  // Restaurar estado do sidebar após navegação
  const restoreSidebarState = () => {
    console.log('[useSidebarToggle] Restaurando estado da sidebar')
    const html = getHtml()
    const sidebar = getElement('#sidebar')
    const verticalStyle = getAttr(html, 'data-vertical-style')
    
    // Remover overlay temporário de hover
    removeAttr(html, 'data-icon-overlay')
    console.log('[useSidebarToggle] data-icon-overlay removido')
    
    // Restaurar estado salvo
    const savedToggled = loadFromLocalStorage()
    console.log('[useSidebarToggle] Estado salvo no localStorage:', savedToggled)
    
    if (savedToggled !== null && window.innerWidth >= 992) {
      if (savedToggled) {
        setAttr(html, 'data-toggled', savedToggled)
        console.log('[useSidebarToggle] data-toggled restaurado:', savedToggled)
      } else {
        removeAttr(html, 'data-toggled')
        console.log('[useSidebarToggle] data-toggled removido - sidebar aberta')
      }
      
      // Re-adicionar listeners se necessário
      if (verticalStyle === 'overlay' && savedToggled === 'icon-overlay-close') {
        if (sidebar) {
          sidebar.removeEventListener('mouseenter', mouseEntered)
          sidebar.removeEventListener('mouseleave', mouseLeave)
          sidebar.addEventListener('mouseenter', mouseEntered)
          sidebar.addEventListener('mouseleave', mouseLeave)
          console.log('[useSidebarToggle] Listeners de hover re-adicionados')
        }
      }
    }
  }
  
  // Inicializar estado do sidebar baseado nos atributos HTML e localStorage
  const initializeSidebar = () => {
    console.log('[useSidebarToggle] Inicializando sidebar')
    const html = getHtml()
    const sidebar = getElement('#sidebar')
    const currentToggled = getAttr(html, 'data-toggled')
    const verticalStyle = getAttr(html, 'data-vertical-style')
    
    console.log('[useSidebarToggle] Estado inicial:', {
      currentToggled,
      verticalStyle,
      windowWidth: window.innerWidth
    })
    
    // Se está em overlay mode e toggled, adicionar event listeners
    if (verticalStyle === 'overlay' && currentToggled === 'icon-overlay-close') {
      if (sidebar && window.innerWidth >= 992) {
        sidebar.addEventListener('mouseenter', mouseEntered)
        sidebar.addEventListener('mouseleave', mouseLeave)
        console.log('[useSidebarToggle] Listeners de hover adicionados')
      }
    }
  }
  
  const toggleSidebar = () => {
    console.log('[useSidebarToggle] Toggle sidebar iniciado')
    const html = getHtml()
    const sidebar = getElement('#sidebar')
    const currentToggled = getAttr(html, 'data-toggled')
    const verticalStyle = getAttr(html, 'data-vertical-style')
    
    console.log('[useSidebarToggle] Estado atual:', {
      currentToggled,
      verticalStyle,
      windowWidth: window.innerWidth
    })

    if (window.innerWidth >= 992) {
      // Desktop
      const navStyle = getAttr(html, 'data-nav-style')
      let newToggled = null
      
      switch (verticalStyle) {
        case 'overlay':
          if (currentToggled === 'icon-overlay-close') {
            html.removeAttribute('data-toggled')
            sidebar?.removeEventListener('mouseenter', mouseEntered)
            sidebar?.removeEventListener('mouseleave', mouseLeave)
            newToggled = null
          } else {
            html.setAttribute('data-toggled', 'icon-overlay-close')
            newToggled = 'icon-overlay-close'
            if (window.innerWidth >= 992) {
              sidebar?.addEventListener('mouseenter', mouseEntered)
              sidebar?.addEventListener('mouseleave', mouseLeave)
            }
          }
          break
          
        case 'closed':
          if (currentToggled === 'close-menu-close') {
            html.removeAttribute('data-toggled')
            newToggled = null
          } else {
            html.setAttribute('data-toggled', 'close-menu-close')
            newToggled = 'close-menu-close'
          }
          break
          
        default:
          // Se não tem vertical-style nem nav-style, usar comportamento overlay (padrão do template)
          if (!verticalStyle && !navStyle) {
            html.setAttribute('data-nav-layout', 'vertical')
            html.setAttribute('data-vertical-style', 'overlay')
            html.removeAttribute('data-nav-style')
            
            if (currentToggled === 'icon-overlay-close') {
              html.removeAttribute('data-toggled')
              sidebar?.removeEventListener('mouseenter', mouseEntered)
              sidebar?.removeEventListener('mouseleave', mouseLeave)
              newToggled = null
            } else {
              html.setAttribute('data-toggled', 'icon-overlay-close')
              newToggled = 'icon-overlay-close'
              if (window.innerWidth >= 992) {
                sidebar?.addEventListener('mouseenter', mouseEntered)
                sidebar?.addEventListener('mouseleave', mouseLeave)
              }
            }
          }
          // Menu click/hover styles
          else if (navStyle === 'menu-click') {
            if (currentToggled === 'menu-click-closed') {
              html.removeAttribute('data-toggled')
              newToggled = null
            } else {
              html.setAttribute('data-toggled', 'menu-click-closed')
              newToggled = 'menu-click-closed'
            }
          }
      }
      
      // Salvar no localStorage
      console.log('[useSidebarToggle] Salvando estado no localStorage:', newToggled)
      saveToLocalStorage(newToggled)
      isOpen.value = !html.getAttribute('data-toggled')
    } else {
      // Mobile
      if (currentToggled === 'close') {
        html.setAttribute('data-toggled', 'open')
        
        // Criar overlay
        let overlay = document.getElementById('responsive-overlay')
        if (!overlay) {
          overlay = document.createElement('div')
          overlay.id = 'responsive-overlay'
          document.body.appendChild(overlay)
        }
        
        setTimeout(() => {
          overlay.classList.add('active')
          overlay.addEventListener('click', closeSidebar)
        }, 100)
        
        isOpen.value = true
      } else {
        closeSidebar()
      }
    }
  }

  const closeSidebar = () => {
    const html = document.querySelector('html')
    html.setAttribute('data-toggled', 'close')
    
    const overlay = document.getElementById('responsive-overlay')
    if (overlay) {
      overlay.classList.remove('active')
    }
    
    isOpen.value = false
  }

  return {
    isOpen,
    toggleSidebar,
    closeSidebar,
    restoreSidebarState,
    initializeSidebar
  }
}
