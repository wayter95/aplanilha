<!--
    ButtonGroup - Componente para agrupar botões
    
    Usado para criar grupos de botões relacionados
-->

<template>
    <div :class="groupClasses">
        <slot></slot>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    // Orientação
    orientation: {
        type: String,
        default: 'horizontal',
        validator: (value) => ['horizontal', 'vertical'].includes(value)
    },
    
    // Tamanho dos botões no grupo
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    
    // Classes customizadas
    customClass: {
        type: String,
        default: ''
    }
})

const groupClasses = computed(() => {
    const classes = []
    
    if (props.orientation === 'horizontal') {
        classes.push('inline-flex', 'rounded-md', 'shadow-sm')
    } else {
        classes.push('inline-flex', 'flex-col')
    }
    
    if (props.customClass) {
        classes.push(props.customClass)
    }
    
    return classes.join(' ')
})
</script>
