/**
 * Composable para gerenciar dark mode / tema
 * Uso: const { isDark, toggleTheme } = useTheme()
 */

import { ref, onMounted } from 'vue'
import { getHtml, addClass, removeClass, hasClass, setAttr } from '@/utils/dom'
import { setItem, removeItem } from '@/utils/localStorage'

export function useTheme() {
  const isDark = ref(false)

  const setDarkMode = () => {
    const html = getHtml()
    addClass(html, 'dark')
    removeClass(html, 'light')
    setAttr(html, 'class', 'dark')
    setAttr(html, 'data-menu-styles', 'dark')
    setAttr(html, 'data-header-styles', 'dark')
    setItem('ynexdarktheme', 'true')
    isDark.value = true
  }

  const setLightMode = () => {
    const html = getHtml()
    removeClass(html, 'dark')
    addClass(html, 'light')
    setAttr(html, 'class', 'light')
    setAttr(html, 'data-menu-styles', 'dark')
    setAttr(html, 'data-header-styles', 'light')
    removeItem('ynexdarktheme')
    isDark.value = false
  }

  const toggleTheme = () => {
    if (isDark.value) {
      setLightMode()
    } else {
      setDarkMode()
    }
  }

  const init = () => {
    const html = getHtml()
    isDark.value = hasClass(html, 'dark')
  }

  onMounted(() => {
    init()
  })

  return {
    isDark,
    toggleTheme,
    setDarkMode,
    setLightMode
  }
}
