<!--
    🔤 BaseInput - Componente base para campos de entrada com validação
    
    Componente reutilizável que integra VeeValidate com design system
    seguindo padrões de nomenclatura e multi-tenant
-->

<template>
    <div class="form-group">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="inputId" 
            class="form-label text-default"
            :class="{ 'required': required }"
        >
            {{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Input Field -->
        <div class="input-group">
            <!-- Left Icon -->
            <span v-if="leftIcon" class="input-group-text">
                <i :class="leftIcon"></i>
            </span>

            <!-- Input -->
            <input
                :id="inputId"
                :name="name"
                :type="type"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :autocomplete="autocomplete"
                :class="inputClasses"
                v-bind="field"
                @blur="handleBlur"
                @input="handleInput"
            />

            <!-- Right Icon -->
            <span v-if="rightIcon" class="input-group-text">
                <i :class="rightIcon"></i>
            </span>

            <!-- Password Toggle -->
            <span 
                v-if="type === 'password' && showPasswordToggle" 
                class="input-group-text cursor-pointer"
                @click="togglePassword"
            >
                <i :class="passwordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
            </span>
        </div>

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
import { computed, ref, nextTick, useSlots } from 'vue'
import { useField } from 'vee-validate'

/**
 * 📝 Props do componente
 */
const props = defineProps({
    // Configuração básica
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text',
        validator: (value) => [
            'text', 'email', 'password', 'number', 'tel', 
            'url', 'search', 'date', 'datetime-local', 'time'
        ].includes(value)
    },
    placeholder: {
        type: String,
        default: ''
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
    
    // Funcionalidades específicas
    showPasswordToggle: {
        type: Boolean,
        default: true
    },
    autocomplete: {
        type: String,
        default: 'off'
    },
    
    // Mensagens
    helpText: {
        type: String,
        default: ''
    },
    successMessage: {
        type: String,
        default: ''
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
    validateOnValueUpdate: false
})

/**
 * 🎯 ID único para o input
 */
const inputId = computed(() => `input-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

/**
 * 👁️ Controle de visibilidade da senha
 */
const passwordVisible = ref(false)
const currentType = ref(props.type)

function togglePassword() {
    passwordVisible.value = !passwordVisible.value
    currentType.value = passwordVisible.value ? 'text' : 'password'
}

/**
 * 🎨 Classes dinâmicas do input
 */
const inputClasses = computed(() => {
    const baseClasses = ['form-control']
    
    // Tamanho
    if (props.size === 'sm') baseClasses.push('form-control-sm')
    if (props.size === 'lg') baseClasses.push('form-control-lg')
    
    // Estado de validação
    if (meta.value.touched) {
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
}

/**
 * 🎬 Emits
 */
const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input'])

// Exposição para acesso externo
defineExpose({
    focus: () => {
        nextTick(() => {
            document.getElementById(inputId.value)?.focus()
        })
    },
    blur: () => {
        nextTick(() => {
            document.getElementById(inputId.value)?.blur()
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

.cursor-pointer {
    cursor: pointer;
}

.input-group-text {
    background-color: var(--bs-gray-50);
    border-color: var(--bs-gray-300);
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