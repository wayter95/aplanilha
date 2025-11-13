import { onMounted, onUnmounted } from 'vue'

/**
 * Composable para gerenciar Tooltips (substitui data-hs-tooltip do Preline)
 * 
 * Uso automático: Chame initTooltips() no onMounted do componente
 * Qualquer elemento com data-hs-tooltip será automaticamente convertido
 */

let tooltipInstance = null

const createTooltipElement = (content) => {
    const tooltip = document.createElement('div')
    tooltip.className = 'hs-tooltip-content ti-main-tooltip !py-1 !px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:!bg-bodybg absolute z-[100] opacity-0 transition-opacity duration-150'
    tooltip.style.pointerEvents = 'none'
    tooltip.innerHTML = content
    tooltip.role = 'tooltip'
    
    document.body.appendChild(tooltip)
    return tooltip
}

const positionTooltip = (trigger, tooltip) => {
    const rect = trigger.getBoundingClientRect()
    const tooltipRect = tooltip.getBoundingClientRect()
    
    // Posicionar acima do elemento por padrão
    const top = rect.top - tooltipRect.height - 8
    const left = rect.left + (rect.width / 2) - (tooltipRect.width / 2)
    
    tooltip.style.top = `${top + window.scrollY}px`
    tooltip.style.left = `${left + window.scrollX}px`
}

const showTooltip = (event) => {
    const trigger = event.currentTarget
    const content = trigger.getAttribute('data-hs-tooltip-content') || trigger.getAttribute('title')
    
    if (!content || tooltipInstance) return
    
    // Remover atributo title para prevenir tooltip nativo
    if (trigger.hasAttribute('title')) {
        trigger.setAttribute('data-original-title', trigger.getAttribute('title'))
        trigger.removeAttribute('title')
    }
    
    tooltipInstance = createTooltipElement(content)
    positionTooltip(trigger, tooltipInstance)
    
    // Trigger animation
    setTimeout(() => {
        if (tooltipInstance) {
            tooltipInstance.style.opacity = '1'
        }
    }, 10)
}

const hideTooltip = (event) => {
    if (!tooltipInstance) return
    
    const trigger = event.currentTarget
    
    // Restaurar title original se existir
    if (trigger.hasAttribute('data-original-title')) {
        trigger.setAttribute('title', trigger.getAttribute('data-original-title'))
        trigger.removeAttribute('data-original-title')
    }
    
    tooltipInstance.style.opacity = '0'
    
    setTimeout(() => {
        if (tooltipInstance) {
            tooltipInstance.remove()
            tooltipInstance = null
        }
    }, 150)
}

/**
 * Inicializa tooltips para todos os elementos com data-hs-tooltip
 * Chame isso no onMounted do componente
 */
export function initTooltips() {
    const triggers = document.querySelectorAll('[data-hs-tooltip], [data-hs-tooltip-content]')
    
    triggers.forEach(trigger => {
        // Remover listeners antigos se existirem
        trigger.removeEventListener('mouseenter', showTooltip)
        trigger.removeEventListener('mouseleave', hideTooltip)
        trigger.removeEventListener('focus', showTooltip)
        trigger.removeEventListener('blur', hideTooltip)
        
        // Adicionar novos listeners
        trigger.addEventListener('mouseenter', showTooltip)
        trigger.addEventListener('mouseleave', hideTooltip)
        trigger.addEventListener('focus', showTooltip)
        trigger.addEventListener('blur', hideTooltip)
    })
}

/**
 * Remove todos os tooltips
 * Chame isso no onUnmounted do componente
 */
export function destroyTooltips() {
    const triggers = document.querySelectorAll('[data-hs-tooltip], [data-hs-tooltip-content]')
    
    triggers.forEach(trigger => {
        trigger.removeEventListener('mouseenter', showTooltip)
        trigger.removeEventListener('mouseleave', hideTooltip)
        trigger.removeEventListener('focus', showTooltip)
        trigger.removeEventListener('blur', hideTooltip)
        
        // Restaurar title original se existir
        if (trigger.hasAttribute('data-original-title')) {
            trigger.setAttribute('title', trigger.getAttribute('data-original-title'))
            trigger.removeAttribute('data-original-title')
        }
    })
    
    if (tooltipInstance) {
        tooltipInstance.remove()
        tooltipInstance = null
    }
}

/**
 * Composable para usar em componentes Vue
 */
export function useTooltip() {
    onMounted(() => {
        initTooltips()
    })
    
    onUnmounted(() => {
        destroyTooltips()
    })
    
    return {
        initTooltips,
        destroyTooltips
    }
}
