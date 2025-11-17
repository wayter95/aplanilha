<!--
    🎨 ColorInput - Componente para seleção de cores com validação
    
    Componente reutilizável que integra VeeValidate com color picker
    seguindo padrões do template Ynex
-->

<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="inputId" 
      class="form-label text-defaulttextcolor"
    >
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    
    <div class="relative flex items-center gap-3">
      <!-- Color Preview Circle -->
      <div class="color-preview-wrapper" :class="{ 'circle-only': variant === 'circle' }">
        <div 
          class="color-preview"
          :style="{ backgroundColor: fieldValue }"
          :class="{ 'disabled': disabled }"
          :title="variant === 'circle' ? fieldValue : 'Escolha a cor'"
        ></div>
        <input
          :id="`${inputId}-picker`"
          type="color"
          :value="fieldValue"
          :disabled="disabled"
          class="color-picker-input"
          @input="handleColorPickerInput"
          @blur="handleBlur"
          :title="variant === 'circle' ? fieldValue : 'Escolha a cor'"
        />
      </div>
      
      <!-- Text Input para HEX (apenas se variant = 'full') -->
      <input 
        v-if="variant === 'full'"
        type="text"
        :id="inputId"
        :name="name"
        :placeholder="placeholder"
        :value="fieldValue"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
        v-bind="fieldProps"
        @input="handleTextInput"
        @blur="handleBlur"
        @focus="$emit('focus', $event)"
        maxlength="7"
      >
      
      <!-- Input hidden para validação quando variant = 'circle' -->
      <input
        v-else
        type="hidden"
        :id="inputId"
        :name="name"
        :value="fieldValue"
        v-bind="fieldProps"
      >
    </div>
    
    <!-- Prioriza erro de validação do VeeValidate, depois o erro customizado -->
    <p v-if="displayError" class="text-danger text-xs mt-1">{{ displayError }}</p>
    <p v-if="help && !displayError" class="text-textmuted text-xs mt-1">{{ help }}</p>
  </div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '#000000'
  },
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: '#000000'
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  help: {
    type: String,
    default: ''
  },
  // Variante do componente
  variant: {
    type: String,
    default: 'full', // 'full' = círculo + input, 'circle' = apenas círculo
    validator: (value) => ['full', 'circle'].includes(value)
  },
  // Props para integração com VeeValidate
  rules: {
    type: [String, Function, Object, Array],
    default: ''
  },
  validateOnBlur: {
    type: Boolean,
    default: true
  },
  validateOnInput: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

// Usar VeeValidate se houver um nome de campo definido
const fieldName = computed(() => props.name || props.id)

const {
  value: fieldValue,
  errorMessage,
  handleBlur: veeHandleBlur,
  handleChange,
  meta,
  ...fieldProps
} = useField(fieldName, props.rules, {
  validateOnValueUpdate: props.validateOnInput,
  validateOnBlur: props.validateOnBlur,
  initialValue: props.modelValue
})

// ID único para o input
const inputId = computed(() => props.id)

// Classes dinâmicas do input
const inputClasses = computed(() => {
  const classes = ['form-control']
  
  // Validação
  if (meta.touched && errorMessage.value) {
    classes.push('is-invalid')
  } else if (meta.touched && meta.valid) {
    classes.push('is-valid')
  }
  
  return classes.join(' ')
})

// Mensagem de erro a ser exibida (prioriza VeeValidate)
const displayError = computed(() => errorMessage.value || props.error)

// Validar se é um código HEX válido
function isValidHex(color) {
  return /^#[0-9A-F]{6}$/i.test(color)
}

// Normalizar cor para formato HEX
function normalizeColor(color) {
  if (!color) return '#000000'
  
  // Se não começar com #, adiciona
  if (!color.startsWith('#')) {
    color = '#' + color
  }
  
  // Remove espaços
  color = color.trim().toUpperCase()
  
  // Valida formato HEX
  if (isValidHex(color)) {
    return color
  }
  
  return fieldValue.value || '#000000'
}

// Handler para o color picker visual
function handleColorPickerInput(event) {
  const newValue = event.target.value
  fieldValue.value = newValue
  handleChange(newValue)
  emit('update:modelValue', newValue)
}

// Handler para o input de texto
function handleTextInput(event) {
  let newValue = event.target.value
  
  // Permite digitar enquanto está incompleto
  if (newValue.length <= 7) {
    fieldValue.value = newValue
    
    // Só valida e emite quando estiver completo
    if (isValidHex(newValue)) {
      handleChange(newValue)
      emit('update:modelValue', newValue)
    }
  }
}

// Handler para blur
function handleBlur(event) {
  // Normaliza a cor ao perder o foco
  const normalized = normalizeColor(fieldValue.value)
  fieldValue.value = normalized
  handleChange(normalized)
  emit('update:modelValue', normalized)
  veeHandleBlur(event)
  emit('blur', event)
}

// Expor métodos e propriedades úteis
defineExpose({
  focus: () => {
    document.getElementById(inputId.value)?.focus()
  },
  blur: () => {
    document.getElementById(inputId.value)?.blur()
  },
  value: fieldValue,
  errorMessage,
  meta
})
</script>

<style scoped>
/* Color Preview Wrapper */
.color-preview-wrapper {
  position: relative;
  width: 48px;
  height: 48px;
  flex-shrink: 0;
}

/* Color Preview Circle */
.color-preview {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15), 
              0 0 0 1px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
}

.color-preview:hover:not(.disabled) {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), 
              0 0 0 2px rgb(var(--primary-rgb));
}

.color-preview.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Hidden Color Picker Input */
.color-picker-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  z-index: 2;
}

.color-picker-input:disabled {
  cursor: not-allowed;
}

/* Text Input Styles */
.form-control {
  flex: 1;
  text-transform: uppercase;
  font-family: 'Courier New', monospace;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.form-control.is-invalid {
  border-color: rgb(var(--danger-rgb));
}

.form-control.is-valid {
  border-color: rgb(var(--success-rgb));
}

/* Checkerboard pattern for transparency */
.color-preview::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background-image: 
    linear-gradient(45deg, #e0e0e0 25%, transparent 25%),
    linear-gradient(-45deg, #e0e0e0 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, #e0e0e0 75%),
    linear-gradient(-45deg, transparent 75%, #e0e0e0 75%);
  background-size: 8px 8px;
  background-position: 0 0, 0 4px, 4px -4px, -4px 0px;
  z-index: -1;
}

/* Variante apenas círculo */
.color-preview-wrapper.circle-only {
  margin: 0 auto;
}

.color-preview-wrapper.circle-only .color-preview {
  cursor: pointer;
}
</style>
