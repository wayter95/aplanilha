<!--
    Button - Componente de botão reutilizável baseado no template Ynex
    
    Suporta todos os estilos e variantes do design system
-->

<template>
    <component
        :is="tag"
        :type="tag === 'button' ? type : undefined"
        :href="tag === 'a' ? href : undefined"
        :to="tag === 'router-link' ? to : undefined"
        :disabled="disabled || loading"
        :class="buttonClasses"
        @click="handleClick"
    >
        <!-- Loading Spinner -->
        <span v-if="loading" class="ti-spinner me-2"></span>
        
        <!-- Left Icon -->
        <i v-if="leftIcon && !loading" :class="[leftIcon, { 'me-1': hasSlot || label }]"></i>
        
        <!-- Content -->
        <slot>{{ label }}</slot>
        
        <!-- Right Icon -->
        <i v-if="rightIcon" :class="[rightIcon, { 'ms-1': hasSlot || label }]"></i>
    </component>
</template>

<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
    // Tipo de elemento
    tag: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'a', 'router-link'].includes(value)
    },
    
    // Tipo do botão (quando tag = button)
    type: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'submit', 'reset'].includes(value)
    },
    
    // Link (quando tag = a)
    href: {
        type: String,
        default: ''
    },
    
    // Rota (quando tag = router-link)
    to: {
        type: [String, Object],
        default: ''
    },
    
    // Conteúdo do botão
    label: {
        type: String,
        default: ''
    },
    
    // Variante de cor
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => [
            'primary', 'secondary', 'success', 'danger', 'warning', 
            'info', 'light', 'dark', 'purple', 'orange', 'teal', 'link'
        ].includes(value)
    },
    
    // Tipo de estilo
    styleType: {
        type: String,
        default: 'solid',
        validator: (value) => [
            'solid', 'outline', 'ghost', 'gradient', 'soft', 'link'
        ].includes(value)
    },
    
    // Tamanho
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    
    // Forma
    shape: {
        type: String,
        default: 'rounded',
        validator: (value) => ['rounded', 'pill', 'square'].includes(value)
    },
    
    // Largura
    width: {
        type: String,
        default: 'auto',
        validator: (value) => ['auto', 'full', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    
    // Ícones
    leftIcon: {
        type: String,
        default: ''
    },
    rightIcon: {
        type: String,
        default: ''
    },
    
    // Estados
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    active: {
        type: Boolean,
        default: false
    },
    
    // Efeitos visuais
    wave: {
        type: Boolean,
        default: true
    },
    shadow: {
        type: Boolean,
        default: false
    },
    outline: {
        type: Boolean,
        default: false
    },
    
    // Classes customizadas
    customClass: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['click'])
const slots = useSlots()

const hasSlot = computed(() => !!slots.default)

const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit('click', event)
    }
}

const buttonClasses = computed(() => {
    const classes = ['ti-btn']
    
    // Wave effect
    if (props.wave && !props.disabled) {
        classes.push('btn-wave')
    }
    
    // Variant and Style Type
    const variantClass = getVariantClass()
    if (variantClass) {
        classes.push(variantClass)
    }
    
    // Size
    const sizeClass = getSizeClass()
    if (sizeClass) {
        classes.push(sizeClass)
    }
    
    // Shape
    if (props.shape === 'pill') {
        classes.push('!rounded-full')
    } else if (props.shape === 'square') {
        classes.push('!rounded-none')
    }
    
    // Width
    const widthClass = getWidthClass()
    if (widthClass) {
        classes.push(widthClass)
    }
    
    // Shadow
    if (props.shadow) {
        classes.push(`shadow-${props.variant}`)
    }
    
    // Active state
    if (props.active) {
        classes.push('active')
    }
    
    // Disabled/Loading state
    if (props.disabled || props.loading) {
        classes.push('opacity-50 cursor-not-allowed')
    }
    
    // Remove outline
    if (props.outline) {
        classes.push('focus:outline-none focus:ring-0')
    }
    
    // Custom classes
    if (props.customClass) {
        classes.push(props.customClass)
    }
    
    return classes.join(' ')
})

const getVariantClass = () => {
    const { variant, styleType } = props
    
    if (styleType === 'link') {
        return 'ti-btn-link'
    }
    
    const styleMap = {
        'solid': `ti-btn-${variant}-full`,
        'outline': `ti-btn-outline-${variant}`,
        'ghost': `ti-btn-ghost-${variant}`,
        'gradient': `ti-btn-${variant}-gradient`,
        'soft': `ti-btn-${variant}`
    }
    
    return styleMap[styleType] || styleMap.solid
}

const getSizeClass = () => {
    const sizeMap = {
        'xs': '!py-1 !px-2 !text-[0.625rem] !font-medium',
        'sm': '!py-1.5 !px-3 !text-[0.75rem] !font-medium',
        'md': '!font-medium', // Default size
        'lg': '!py-2.5 !px-4 !text-[0.875rem] !font-medium',
        'xl': '!py-3 !px-5 !text-base !font-medium'
    }
    
    return sizeMap[props.size] || ''
}

const getWidthClass = () => {
    const widthMap = {
        'auto': '',
        'full': 'w-full',
        'sm': 'ti-btn-w-sm',
        'md': 'ti-btn-w-md',
        'lg': 'ti-btn-w-lg',
        'xl': 'ti-btn-w-xl'
    }
    
    return widthMap[props.width] || ''
}
</script>

<style scoped>
/* Loading spinner animation */
.ti-spinner {
    display: inline-block;
    width: 1em;
    height: 1em;
    border: 2px solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}
</style>
