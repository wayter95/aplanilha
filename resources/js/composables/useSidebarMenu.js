import { ref, onMounted } from 'vue'

export function useSidebarMenu() {
  const ANIMATION_DURATION = 300

  const slideUp = (target, duration = ANIMATION_DURATION) => {
    const { parentElement } = target
    parentElement.classList.remove('open')
    target.style.transitionProperty = 'height, margin, padding'
    target.style.transitionDuration = `${duration}ms`
    target.style.boxSizing = 'border-box'
    target.style.height = `${target.offsetHeight}px`
    target.offsetHeight
    target.style.overflow = 'hidden'
    target.style.height = 0
    target.style.paddingTop = 0
    target.style.paddingBottom = 0
    target.style.marginTop = 0
    target.style.marginBottom = 0
    window.setTimeout(() => {
      target.style.display = 'none'
      target.style.removeProperty('height')
      target.style.removeProperty('padding-top')
      target.style.removeProperty('padding-bottom')
      target.style.removeProperty('margin-top')
      target.style.removeProperty('margin-bottom')
      target.style.removeProperty('overflow')
      target.style.removeProperty('transition-duration')
      target.style.removeProperty('transition-property')
    }, duration)
  }

  const slideDown = (target, duration = ANIMATION_DURATION) => {
    const { parentElement } = target
    parentElement.classList.add('open')
    target.style.removeProperty('display')
    let { display } = window.getComputedStyle(target)
    if (display === 'none') display = 'block'
    target.style.display = display
    const height = target.offsetHeight
    target.style.overflow = 'hidden'
    target.style.height = 0
    target.style.paddingTop = 0
    target.style.paddingBottom = 0
    target.style.marginTop = 0
    target.style.marginBottom = 0
    target.offsetHeight
    target.style.boxSizing = 'border-box'
    target.style.transitionProperty = 'height, margin, padding'
    target.style.transitionDuration = `${duration}ms`
    target.style.height = `${height}px`
    target.style.removeProperty('padding-top')
    target.style.removeProperty('padding-bottom')
    target.style.removeProperty('margin-top')
    target.style.removeProperty('margin-bottom')
    window.setTimeout(() => {
      target.style.removeProperty('height')
      target.style.removeProperty('overflow')
      target.style.removeProperty('transition-duration')
      target.style.removeProperty('transition-property')
    }, duration)
  }

  const slideToggle = (target, duration = ANIMATION_DURATION) => {
    const html = document.querySelector('html')
    if (
      !(
        (html.getAttribute('data-nav-style') === 'menu-hover' &&
          html.getAttribute('data-toggled') === 'menu-hover-closed' &&
          window.innerWidth >= 992) ||
        (html.getAttribute('data-nav-style') === 'icon-hover' &&
          html.getAttribute('data-toggled') === 'icon-hover-closed' &&
          window.innerWidth >= 992)
      ) &&
      target &&
      target.nodeType != 3
    ) {
      if (window.getComputedStyle(target).display === 'none') {
        return slideDown(target, duration)
      }
      return slideUp(target, duration)
    }
  }

  const handleMenuClick = (event) => {
    const menuItem = event.currentTarget
    const submenu = menuItem.nextElementSibling
    
    if (!submenu) return

    const html = document.querySelector('html')
    const isHoverStyle = html.getAttribute('data-nav-style') === 'menu-hover' || 
                        html.getAttribute('data-nav-style') === 'icon-hover'
    
    if (isHoverStyle && window.innerWidth >= 992) return

    // Fechar outros menus do mesmo nível
    const parentMenu = menuItem.closest('.nav.sub-open')
    if (parentMenu) {
      parentMenu.querySelectorAll(':scope > ul > .slide.has-sub > a').forEach((el) => {
        if (el !== menuItem && el.nextElementSibling) {
          const siblingSubmenu = el.nextElementSibling
          if (window.getComputedStyle(siblingSubmenu).display === 'block') {
            slideUp(siblingSubmenu)
          }
        }
      })
    }

    slideToggle(submenu)
  }

  const initializeMenu = () => {
    // Abrir menus que já estão marcados como open
    const defaultOpenMenus = document.querySelectorAll('.slide.has-sub.open')
    defaultOpenMenus.forEach((element) => {
      if (element.lastElementChild) {
        element.lastElementChild.style.display = 'block'
      }
    })
  }

  onMounted(() => {
    initializeMenu()
  })

  return {
    handleMenuClick,
    initializeMenu
  }
}
