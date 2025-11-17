<template>
  <span :class="badgeClasses">
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: false,
    default: null
  },
  variant: {
    type: String,
    default: null,
    validator: (value) => !value || ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'black'].includes(value)
  },
  customLabel: {
    type: String,
    default: null
  },
  rounded: {
    type: Boolean,
    default: false
  }
})

// Mapeamento de status para labels padrão
const statusLabels = {
  'a': 'Ativo',
  'active': 'Ativo',
  'b': 'Bloqueado',
  'blocked': 'Bloqueado',
  'inactive': 'Inativo',
  'pending': 'Pendente',
  'approved': 'Aprovado',
  'rejected': 'Rejeitado'
}

// Mapeamento de status para variantes do Ynex
// IMPORTANTE: Status ativo = PRIMARY (não success)
const statusVariants = {
  'a': 'primary',
  'active': 'primary',
  'b': 'danger',
  'blocked': 'danger',
  'inactive': 'light',
  'pending': 'warning',
  'approved': 'success',
  'rejected': 'danger'
}

const label = computed(() => {
  if (props.customLabel) return props.customLabel
  if (props.status && statusLabels[props.status]) {
    return statusLabels[props.status]
  }
  return props.status || 'Badge'
})

const badgeClasses = computed(() => {
  // Se variant foi especificado explicitamente, usa ele
  let variant = props.variant
  
  // Se não tem variant mas tem status, mapeia status para variant
  if (!variant && props.status) {
    variant = statusVariants[props.status] || 'secondary'
  }
  
  // Se não tem nem variant nem status, usa secondary
  if (!variant) {
    variant = 'secondary'
  }
  
  // Monta as classes base
  const baseClasses = ['badge']
  
  // Adiciona cor de fundo e texto
  if (variant === 'light') {
    baseClasses.push('bg-light', 'text-dark')
  } else if (variant === 'black') {
    baseClasses.push('bg-black', 'text-white')
  } else {
    baseClasses.push(`bg-${variant}`, 'text-white')
  }
  
  // Adiciona rounded se necessário
  if (props.rounded) {
    baseClasses.push('rounded-full')
  }
  
  return baseClasses.join(' ')
})
</script>
