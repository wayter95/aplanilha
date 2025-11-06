<!--
    📋 Select - Componente para campos de seleção com validação
    
    Componente reutilizável que integra VeeValidate com design system
    suporta options simples e complexas para multi-tenant
-->

<template>
    <div class="form-group">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="selectId" 
            class="form-label text-default"
            :class="{ 'required': required }"
        >
            {{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Select Field -->
        <div class="input-group">
            <!-- Left Icon -->
            <span v-if="leftIcon" class="input-group-text">
                <i :class="leftIcon"></i>
            </span>

            <!-- Select -->
            <select
                :id="selectId"
                :name="name"
                :disabled="disabled"
                :class="selectClasses"
                v-bind="field"
                @blur="handleBlur"
                @change="handleChange"
            >
                <!-- Placeholder Option -->
                <option v-if="placeholder" value="" disabled>
                    {{ placeholder }}
                </option>

                <!-- Options -->
                <option
                    v-for="option in normalizedOptions"
                    :key="option.value"
                    :value="option.value"
                    :disabled="option.disabled"
                >
                    {{ option.label }}
                </option>

                <!-- Grouped Options -->
                <optgroup
                    v-for="group in groupedOptions"
                    :key="group.label"
                    :label="group.label"
                >
                    <option
                        v-for="option in group.options"
                        :key="option.value"
                        :value="option.value"
                        :disabled="option.disabled"
                    >
                        {{ option.label }}
                    </option>
                </optgroup>
            </select>

            <!-- Right Icon -->
            <span v-if="rightIcon" class="input-group-text">
                <i :class="rightIcon"></i>
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
import { computed, nextTick, watch } from 'vue'
import { useField } from 'vee-validate'

/**
 * 📝 Props do componente
 */
const props = defineProps({
    // v-model
    modelValue: {
        type: [String, Number, Array],
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
        default: 'Selecione uma opção'
    },
    
    // Options
    options: {
        type: Array,
        default: () => [],
        validator: (options) => {
            return options.every(option => {
                // String simples ou objeto com value/label
                return typeof option === 'string' || 
                       (typeof option === 'object' && option.value !== undefined && option.label !== undefined)
            })
        }
    },
    
    // Options agrupadas
    groupedOptions: {
        type: Array,
        default: () => []
    },
    
    // Configuração de valor
    valueKey: {
        type: String,
        default: 'value'
    },
    labelKey: {
        type: String,
        default: 'label'
    },
    disabledKey: {
        type: String,
        default: 'disabled'
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
    
    // Multi-tenant
    multiple: {
        type: Boolean,
        default: false
    }
})

/**
 * 🔧 Configuração do campo com VeeValidate
 */
const { 
    value, 
    errorMessage, 
    handleBlur, 
    handleChange: veeHandleChange,
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
 * 🎯 ID único para o select
 */
const selectId = computed(() => `select-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

/**
 * 📋 Normalização das options
 */
const normalizedOptions = computed(() => {
    return props.options.map(option => {
        if (typeof option === 'string') {
            return {
                value: option,
                label: option,
                disabled: false
            }
        }
        
        return {
            value: option[props.valueKey],
            label: option[props.labelKey],
            disabled: option[props.disabledKey] || false
        }
    })
})

/**
 * 🎨 Classes dinâmicas do select
 */
const selectClasses = computed(() => {
    const baseClasses = ['form-select']
    
    // Tamanho
    if (props.size === 'sm') baseClasses.push('form-select-sm')
    if (props.size === 'lg') baseClasses.push('form-select-lg')
    
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
        baseClasses.push(`form-select-${props.variant}`)
    }
    
    return baseClasses
})

/**
 * 🔄 Field binding para VeeValidate
 */
const field = computed(() => ({
    value: value.value,
    multiple: props.multiple
}))

/**
 * 📝 Manipulação de mudança
 */
function handleChange(event) {
    let newValue = event.target.value
    
    // Para selects múltiplos
    if (props.multiple) {
        newValue = Array.from(event.target.selectedOptions, option => option.value)
    }
    
    value.value = newValue
    veeHandleChange(newValue)
    emit('update:modelValue', newValue)
}

/**
 * 🎬 Emits
 */
const emit = defineEmits(['update:modelValue', 'change'])

// Exposição para acesso externo
defineExpose({
    focus: () => {
        nextTick(() => {
            document.getElementById(selectId.value)?.focus()
        })
    },
    blur: () => {
        nextTick(() => {
            document.getElementById(selectId.value)?.blur()
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
}

.form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.form-select.is-valid {
    border-color: var(--bs-success);
}

.form-select.is-invalid {
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

