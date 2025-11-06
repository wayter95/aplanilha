<!--
    📝 Textarea - Componente para campos de textarea com validação
    
    Componente reutilizável que integra VeeValidate com design system
    seguindo padrões de nomenclatura e multi-tenant
-->

<template>
    <div class="form-group">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="textareaId" 
            class="form-label text-default"
            :class="{ 'required': required }"
        >
            {{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Textarea Field -->
        <div class="input-group">
            <!-- Left Icon -->
            <span v-if="leftIcon" class="input-group-text">
                <i :class="leftIcon"></i>
            </span>

            <!-- Textarea -->
            <textarea
                :id="textareaId"
                :name="name"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :rows="rows"
                :cols="cols"
                :maxlength="maxlength"
                :class="textareaClasses"
                :style="{ resize: props.resize }"
                v-bind="field"
                @blur="handleBlur"
                @input="handleInput"
            ></textarea>

            <!-- Right Icon -->
            <span v-if="rightIcon" class="input-group-text">
                <i :class="rightIcon"></i>
            </span>
        </div>

        <!-- Character Counter -->
        <small v-if="maxlength && showCounter" class="form-text text-muted text-end d-block">
            {{ characterCount }} / {{ maxlength }}
        </small>

        <!-- Help Text -->
        <small v-if="helpText && !errorMessage" class="form-text text-muted">
            {{ helpText }}
        </small>

        <!-- Error Message -->
        <div v-if="errorMessage" class="invalid-feedback d-block">
            <i class="ri-error-warning-line me-1"></i>
            {{ errorMessage }}
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="valid-feedback d-block">
            <i class="ri-check-line me-1"></i>
            {{ successMessage }}
        </div>
    </div>
</template>

<script setup>
import { computed, ref, nextTick, watch } from 'vue'
import { useField } from 'vee-validate'

/**
 * 📝 Props do componente
 */
const props = defineProps({
    // v-model
    modelValue: {
        type: String,
        default: undefined
    },
    // Configuração básica
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    
    // Dimensões
    rows: {
        type: Number,
        default: 3
    },
    cols: {
        type: Number,
        default: undefined
    },
    maxlength: {
        type: Number,
        default: undefined
    },
    
    // Validação
    rules: {
        type: [String, Function, Object],
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    
    // Estados
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    
    // Aparência
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'success', 'warning', 'danger'].includes(value)
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
    
    // Mensagens
    helpText: {
        type: String,
        default: ''
    },
    successMessage: {
        type: String,
        default: ''
    },
    
    // Funcionalidades
    showCounter: {
        type: Boolean,
        default: false
    },
    resize: {
        type: String,
        default: 'vertical',
        validator: (value) => ['none', 'both', 'horizontal', 'vertical'].includes(value)
    }
})

/**
 * 🔧 Configuração do campo com VeeValidate
 */
const { 
    value, 
    errorMessage, 
    handleBlur, 
    handleChange,
    meta 
} = useField(props.name, props.rules, {
    validateOnValueUpdate: false,
    initialValue: props.modelValue
})

// Sincroniza value interno com modelValue externo
watch(() => props.modelValue, (newVal) => {
    if (newVal !== undefined && value.value !== newVal) {
        value.value = newVal
    }
}, { immediate: true })

/**
 * 🎯 ID único para o textarea
 */
const textareaId = computed(() => `textarea-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

/**
 * 🔢 Contador de caracteres
 */
const characterCount = computed(() => {
    return value.value ? value.value.length : 0
})

/**
 * 🎨 Classes dinâmicas do textarea
 */
const textareaClasses = computed(() => {
    const baseClasses = ['form-control']
    
    // Tamanho
    if (props.size === 'sm') baseClasses.push('form-control-sm')
    if (props.size === 'lg') baseClasses.push('form-control-lg')
    
    // Estado de validação
    if (meta.value && meta.value.touched) {
        if (errorMessage.value) {
            baseClasses.push('is-invalid')
        } else if (meta.value.valid) {
            baseClasses.push('is-valid')
        }
    }
    
    // Variante
    if (props.variant !== 'default') {
        baseClasses.push(`form-control-${props.variant}`)
    }
    
    return baseClasses
})

/**
 * 🔄 Field binding para VeeValidate
 */
const field = computed(() => ({
    value: value.value,
    onInput: handleInput,
    onBlur: handleBlur
}))

/**
 * 📝 Manipulação de input
 */
function handleInput(event) {
    value.value = event.target.value
    handleChange(event.target.value)
    emit('update:modelValue', event.target.value)
}

/**
 * 🎬 Emits
 */
const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input'])

// Exposição para acesso externo
defineExpose({
    focus: () => {
        nextTick(() => {
            document.getElementById(textareaId.value)?.focus()
        })
    },
    blur: () => {
        nextTick(() => {
            document.getElementById(textareaId.value)?.blur()
        })
    },
    value,
    errorMessage,
    meta
})
</script>

<style scoped>
.form-label.required {
    font-weight: 500;
}

.input-group-text {
    background-color: var(--bs-gray-50);
    border-color: var(--bs-gray-300);
    align-items: flex-start;
    padding-top: 0.5rem;
}

textarea.form-control {
    min-height: calc(1.5em + 0.75rem + 2px);
}

.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.form-control.is-valid {
    border-color: var(--bs-success);
}

.form-control.is-invalid {
    border-color: var(--bs-danger);
}

.invalid-feedback,
.valid-feedback {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-text {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>

