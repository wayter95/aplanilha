<!--
    🔄 Switch - Componente para switches/toggles com validação
    
    Componente reutilizável que integra VeeValidate com design system
    seguindo padrões de nomenclatura e multi-tenant
-->

<template>
    <div class="form-group">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="switchId" 
            class="form-label text-default d-flex align-items-center"
            :class="{ 'required': required }"
        >
            <span>{{ label }}</span>
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Switch Container -->
        <div class="d-flex align-items-center gap-3">
            <!-- Left Icon -->
            <span v-if="leftIcon" class="input-group-text">
                <i :class="leftIcon"></i>
            </span>

            <!-- Switch -->
            <div class="form-check form-switch">
                <input
                    :id="switchId"
                    :name="name"
                    type="checkbox"
                    class="ti-switch"
                    :class="switchClasses"
                    :disabled="disabled"
                    :checked="isChecked"
                    @change="handleChange"
                    @blur="handleBlur"
                />
                <label 
                    v-if="!label || showInlineLabel" 
                    :for="switchId" 
                    class="form-check-label ms-2"
                    :class="{ 'text-muted': !isChecked }"
                >
                    {{ inlineLabel || (isChecked ? activeLabel : inactiveLabel) }}
                </label>
            </div>

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
        type: Boolean,
        default: false
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
    
    // Labels inline
    activeLabel: {
        type: String,
        default: 'Ativo'
    },
    inactiveLabel: {
        type: String,
        default: 'Inativo'
    },
    inlineLabel: {
        type: String,
        default: ''
    },
    showInlineLabel: {
        type: Boolean,
        default: false
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
        validator: (value) => ['default', 'primary', 'success', 'warning', 'danger'].includes(value)
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
    initialValue: props.modelValue,
    type: 'checkbox'
})

// Sincroniza value interno com modelValue externo
watch(() => props.modelValue, (newVal) => {
    if (value.value !== newVal) {
        value.value = newVal
    }
}, { immediate: true })

/**
 * 🎯 ID único para o switch
 */
const switchId = computed(() => `switch-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

/**
 * ✅ Estado checked
 */
const isChecked = computed(() => {
    return value.value === true || value.value === 'true' || value.value === 1 || value.value === '1'
})

/**
 * 🎨 Classes dinâmicas do switch
 */
const switchClasses = computed(() => {
    const baseClasses = []
    
    // Tamanho
    if (props.size === 'sm') baseClasses.push('ti-switch-sm')
    if (props.size === 'lg') baseClasses.push('ti-switch-lg')
    
    // Variante (pode ser aplicado via CSS customizado se necessário)
    if (props.variant !== 'default') {
        baseClasses.push(`ti-switch-${props.variant}`)
    }
    
    // Estado de validação
    if (meta.value && meta.value.touched) {
        if (errorMessage.value) {
            baseClasses.push('is-invalid')
        } else if (meta.value.valid) {
            baseClasses.push('is-valid')
        }
    }
    
    return baseClasses
})


/**
 * 📝 Manipulação de mudança
 */
function handleChange(event) {
    const newValue = event.target.checked
    value.value = newValue
    veeHandleChange(newValue)
    emit('update:modelValue', newValue)
    emit('change', newValue)
}

/**
 * 🎬 Emits
 */
const emit = defineEmits(['update:modelValue', 'change', 'blur'])

// Exposição para acesso externo
defineExpose({
    focus: () => {
        nextTick(() => {
            document.getElementById(switchId.value)?.focus()
        })
    },
    blur: () => {
        nextTick(() => {
            document.getElementById(switchId.value)?.blur()
        })
    },
    value,
    errorMessage,
    meta,
    isChecked
})
</script>

<style scoped>
.form-label.required {
    font-weight: 500;
}

.form-label.d-flex {
    display: flex;
    align-items: center;
}

.input-group-text {
    background-color: var(--bs-gray-50);
    border-color: var(--bs-gray-300);
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

.ti-switch:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

.ti-switch.is-valid {
    border-color: var(--bs-success);
}

.ti-switch.is-invalid {
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

/* Variantes de cor customizadas (opcional) */
.ti-switch-primary {
    /* Usa a cor primária padrão do sistema */
}

.ti-switch-success:checked {
    background-color: var(--bs-success);
}

.ti-switch-warning:checked {
    background-color: var(--bs-warning);
}

.ti-switch-danger:checked {
    background-color: var(--bs-danger);
}
</style>

