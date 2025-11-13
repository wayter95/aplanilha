import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Composable para gerenciar Overlays/Offcanvas (substitui data-hs-overlay do Preline)
 * 
 * Uso:
 * const { isOpen, open, close, toggle } = useOverlay('overlay-id')
 */
export function useOverlay(overlayId) {
    const isOpen = ref(false)
    const backdropElement = ref(null)
    const overlayElement = ref(null)

    // Criar e mostrar backdrop
    const showBackdrop = () => {
        if (!backdropElement.value) {
            backdropElement.value = document.createElement('div')
            backdropElement.value.className = 'hs-overlay-backdrop transition duration fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 dark:bg-neutral-900 z-[60]'
            backdropElement.value.onclick = close
            document.body.appendChild(backdropElement.value)
        }
        
        // Trigger animation
        setTimeout(() => {
            backdropElement.value?.classList.add('opacity-100')
        }, 10)
    }

    // Remover backdrop
    const hideBackdrop = () => {
        if (backdropElement.value) {
            backdropElement.value.classList.remove('opacity-100')
            setTimeout(() => {
                backdropElement.value?.remove()
                backdropElement.value = null
            }, 300)
        }
    }

    // Abrir overlay
    const open = () => {
        const overlay = document.getElementById(overlayId)
        if (!overlay) {
            console.warn(`Overlay #${overlayId} not found`)
            return
        }

        overlayElement.value = overlay
        isOpen.value = true

        // Adicionar classes de abertura
        overlay.classList.add('open', 'hs-overlay-open')
        overlay.classList.remove('hidden')
        
        // Mostrar backdrop
        showBackdrop()

        // Prevenir scroll do body
        document.body.style.overflow = 'hidden'

        // Trigger animation
        setTimeout(() => {
            overlay.style.opacity = '1'
            overlay.style.transform = 'translateX(0)'
        }, 10)
    }

    // Fechar overlay
    const close = () => {
        if (!overlayElement.value) return

        isOpen.value = false

        // Remover classes de abertura
        overlayElement.value.style.opacity = '0'
        overlayElement.value.style.transform = 'translateX(100%)' // Para overlays da direita
        
        setTimeout(() => {
            overlayElement.value?.classList.remove('open', 'hs-overlay-open')
            overlayElement.value?.classList.add('hidden')
            overlayElement.value = null
        }, 300)

        // Esconder backdrop
        hideBackdrop()

        // Restaurar scroll do body
        document.body.style.overflow = ''
    }

    // Toggle overlay
    const toggle = () => {
        if (isOpen.value) {
            close()
        } else {
            open()
        }
    }

    // Listener para tecla ESC
    const handleEscape = (event) => {
        if (event.key === 'Escape' && isOpen.value) {
            close()
        }
    }

    // Setup listeners
    onMounted(() => {
        document.addEventListener('keydown', handleEscape)
    })

    // Cleanup
    onUnmounted(() => {
        document.removeEventListener('keydown', handleEscape)
        close() // Fechar overlay se componente for desmontado
    })

    return {
        isOpen,
        open,
        close,
        toggle
    }
}

/**
 * Função helper para inicializar triggers de overlay (botões data-hs-overlay)
 * Chame isso no onMounted do componente que tem botões que abrem overlays
 */
export function initOverlayTriggers() {
    const triggers = document.querySelectorAll('[data-hs-overlay]')
    
    triggers.forEach(trigger => {
        const overlayId = trigger.getAttribute('data-hs-overlay')?.replace('#', '')
        if (!overlayId) return

        trigger.addEventListener('click', (e) => {
            e.preventDefault()
            const overlay = document.getElementById(overlayId)
            if (overlay) {
                // Disparar evento personalizado que o componente overlay pode escutar
                const event = new CustomEvent('overlay-toggle', { detail: { overlayId } })
                window.dispatchEvent(event)
            }
        })
    })
}
