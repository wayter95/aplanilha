<template>
  <span :class="badgeClasses">
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'black'].includes(value)
  },
  label: {
    type: String,
    default: ''
  },
  rounded: {
    type: Boolean,
    default: false
  },
  outline: {
    type: Boolean,
    default: false
  },
  soft: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
  }
})

const badgeClasses = computed(() => {
  const classes = ['badge']
  
  // Estilo do badge (solid, outline ou soft)
  if (props.outline) {
    // Badge com borda
    classes.push(`border`, `border-${props.variant}`, `text-${props.variant}`, 'bg-transparent')
  } else if (props.soft) {
    // Badge com fundo suave (bg-{color}/10)
    if (props.variant === 'light') {
      classes.push('bg-light/20', 'text-dark')
    } else if (props.variant === 'black') {
      classes.push('bg-black/10', 'text-black')
    } else {
      classes.push(`bg-${props.variant}/10`, `text-${props.variant}`)
    }
  } else {
    // Badge sólido (padrão)
    if (props.variant === 'light') {
      classes.push('bg-light', 'text-dark')
    } else if (props.variant === 'black') {
      classes.push('bg-black', 'text-white')
    } else {
      classes.push(`bg-${props.variant}`, 'text-white')
    }
  }
  
  // Formato arredondado
  if (props.rounded) {
    classes.push('rounded-full')
  }
  
  // Tamanho
  switch (props.size) {
    case 'xs':
      classes.push('text-xs', 'px-2', 'py-0.5')
      break
    case 'sm':
      classes.push('text-xs', 'px-2.5', 'py-1')
      break
    case 'lg':
      classes.push('text-sm', 'px-4', 'py-2')
      break
    default: // md
      classes.push('text-xs', 'px-3', 'py-1.5')
  }
  
  return classes.join(' ')
})
</script>
