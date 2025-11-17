<template>
  <component
    :is="componentType"
    :href="href"
    :to="to"
    :class="linkClasses"
    @click="handleClick"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  // Para usar com Inertia
  href: {
    type: String,
    default: null
  },
  // Para usar com Vue Router
  to: {
    type: [String, Object],
    default: null
  },
  // Tipo de link (externo, interno, etc)
  external: {
    type: Boolean,
    default: false
  },
  // Variantes de estilo
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default', 
      'primary', 
      'secondary', 
      'breadcrumb', 
      'nav', 
      'sidebar'
    ].includes(value)
  },
  // Classes adicionais
  customClass: {
    type: String,
    default: ''
  },
  // Desabilitado
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

// Determina qual componente usar
const componentType = computed(() => {
  if (props.disabled) return 'span'
  if (props.external) return 'a'
  if (props.href) return Link
  if (props.to) return 'router-link'
  return 'a'
})

// Classes baseadas na variante
const linkClasses = computed(() => {
  const variants = {
    default: 'text-primary hover:text-primary dark:text-primary',
    primary: 'text-primary hover:underline',
    secondary: 'text-defaulttextcolor hover:text-primary dark:text-white dark:hover:text-primary',
    breadcrumb: 'flex items-center text-primary hover:text-primary dark:text-primary truncate',
    nav: 'block text-defaulttextcolor hover:text-primary dark:text-white dark:hover:text-primary transition-colors',
    sidebar: 'side-menu__item'
  }

  const baseClasses = variants[props.variant] || variants.default
  const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
  
  return `${baseClasses} ${disabledClasses} ${props.customClass}`.trim()
})

const handleClick = (event) => {
  if (props.disabled) {
    event.preventDefault()
    return
  }
  emit('click', event)
}
</script>
