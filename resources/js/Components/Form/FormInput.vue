<!--
    FormInput - Componente de input com validação visual do template Ynex
    
    Tamanho pequeno por padrão, validação visual automática
-->

<template>
    <div class="space-y-2">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="inputId" 
            class="ti-form-label"
        >
            {{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Input Container -->
        <div class="relative">
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
                :value="value"
                @input="handleInput"
                @blur="handleBlur"
                @focus="handleFocus"
            />

            <!-- Validation Icon -->
            <div v-if="showValidationIcon" class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                <!-- Error Icon -->
                <svg v-if="hasError" class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                </svg>
                
                <!-- Success Icon -->
                <svg v-else-if="hasSuccess" class="h-5 w-5 text-success" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="errorMessage" class="text-sm text-red-600 mt-2">
            {{ errorMessage }}
        </p>

        <!-- Success Message -->
        <p v-else-if="successMessage" class="text-sm text-green-600 mt-2">
            {{ successMessage }}
        </p>

        <!-- Help Text -->
        <p v-else-if="helpText" class="text-sm text-gray-600 dark:text-white/70 mt-2">
            {{ helpText }}
        </p>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useField } from 'vee-validate'

const props = defineProps({
    // v-model
    modelValue: {
        type: [String, Number],
        default: ''
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
    type: {
        type: String,
        default: 'text'
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
    
    // Mensagens
    errorMessage: {
        type: String,
        default: ''
    },
    successMessage: {
        type: String,
        default: ''
    },
    helpText: {
        type: String,
        default: ''
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
    
    // Tamanho (sempre sm por padrão)
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    
    // Validação visual
    showValidation: {
        type: Boolean,
        default: true
    },
    
    // Outros
    autocomplete: {
        type: String,
        default: 'off'
    }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input'])

// VeeValidate
const { value, errorMessage: veeError, handleBlur: veeBlur, meta } = useField(
    props.name,
    props.rules,
    {
        validateOnValueUpdate: false,
        initialValue: props.modelValue
    }
)

// ID único
const inputId = computed(() => `input-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

// Estados de validação
const hasError = computed(() => {
    return props.showValidation && (!!props.errorMessage || (meta.touched && !!veeError.value))
})

const hasSuccess = computed(() => {
    return props.showValidation && !!props.successMessage && !hasError.value
})

const showValidationIcon = computed(() => {
    return props.showValidation && (hasError.value || hasSuccess.value)
})

// Classes do input
const inputClasses = computed(() => {
    const classes = ['ti-form-input']
    
    // Tamanho
    if (props.size === 'sm') {
        classes.push('!py-1.5', '!text-[0.75rem]')
    } else if (props.size === 'lg') {
        classes.push('!py-2.5', '!text-base')
    }
    
    // Validação
    if (hasError.value) {
        classes.push('!border-danger', 'focus:border-danger', 'focus:ring-danger')
    } else if (hasSuccess.value) {
        classes.push('!border-success', 'focus:border-success', 'focus:ring-success')
    }
    
    return classes.join(' ')
})

// Handlers
const handleInput = (event) => {
    const newValue = event.target.value
    value.value = newValue
    emit('update:modelValue', newValue)
    emit('input', event)
}

const handleBlur = (event) => {
    veeBlur()
    emit('blur', event)
}

const handleFocus = (event) => {
    emit('focus', event)
}

// Expose para acesso externo
defineExpose({
    value,
    errorMessage: veeError,
    meta
})
</script>
