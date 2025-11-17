<!--
    IconButton - Componente de botão apenas com ícone
    
    Usado para ações rápidas e compactas
-->

<template>
    <Button
        v-bind="buttonProps"
        :custom-class="`ti-btn-icon ${customClass}`"
        @click="handleClick"
    >
        <i v-if="!loading" :class="icon"></i>
        <span v-if="loading" class="ti-spinner"></span>
    </Button>
</template>

<script setup>
import { computed } from 'vue'
import Button from './Button.vue'

const props = defineProps({
    // Ícone
    icon: {
        type: String,
        required: true
    },
    
    // Propriedades herdadas do Button
    variant: {
        type: String,
        default: 'primary'
    },
    styleType: {
        type: String,
        default: 'solid'
    },
    size: {
        type: String,
        default: 'md'
    },
    shape: {
        type: String,
        default: 'rounded'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    wave: {
        type: Boolean,
        default: true
    },
    shadow: {
        type: Boolean,
        default: false
    },
    customClass: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'button'
    },
    tag: {
        type: String,
        default: 'button'
    },
    href: {
        type: String,
        default: ''
    },
    to: {
        type: [String, Object],
        default: ''
    }
})

const emit = defineEmits(['click'])

const buttonProps = computed(() => ({
    variant: props.variant,
    styleType: props.styleType,
    size: props.size,
    shape: props.shape,
    disabled: props.disabled,
    loading: props.loading,
    wave: props.wave,
    shadow: props.shadow,
    type: props.type,
    tag: props.tag,
    href: props.href,
    to: props.to
}))

const handleClick = (event) => {
    emit('click', event)
}
</script>
