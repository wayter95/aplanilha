<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="inputId" 
      class="form-label text-defaulttextcolor block"
    >
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
      <a 
        v-if="forgotPasswordLink" 
        :href="forgotPasswordLink" 
        class="ltr:float-right rtl:float-left text-danger"
      >
        Esqueceu a senha?
      </a>
    </label>
    
    <div class="input-group">
      <input 
        :type="passwordType"
        :id="inputId"
        :name="name"
        :placeholder="placeholder"
        :value="fieldValue"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
        v-bind="fieldProps"
        @input="handleInput"
        @blur="handleBlur"
        @focus="$emit('focus', $event)"
      >
      <button 
        aria-label="button" 
        type="button" 
        class="ti-btn ti-btn-light !rounded-s-none !mb-0" 
        @click="togglePassword"
      >
        <i :class="passwordIcon" class="align-middle"></i>
      </button>
    </div>
    
    <!-- Prioriza erro de validação do VeeValidate, depois o erro customizado -->
    <p v-if="displayError" class="text-danger text-xs mt-1">{{ displayError }}</p>
    <p v-if="help && !displayError" class="text-textmuted text-xs mt-1">{{ help }}</p>
    <div v-if="showRemember" class="mt-2">
      <div class="form-check !ps-0">
        <input 
          class="form-check-input" 
          type="checkbox" 
          :id="`remember-${inputId}`"
          v-model="rememberValue"
          @change="$emit('update:remember', $event.target.checked)"
        >
        <label 
          class="form-check-label text-textmuted font-normal" 
          :for="`remember-${inputId}`"
        >
          Lembrar senha?
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { computed, nextTick, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
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
    default: 'Senha'
  },
  placeholder: {
    type: String,
    default: 'senha'
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
  size: {
    type: String,
    default: 'lg',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  showRemember: {
    type: Boolean,
    default: false
  },
  remember: {
    type: Boolean,
    default: false
  },
  forgotPasswordLink: {
    type: String,
    default: ''
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

const emit = defineEmits(['update:modelValue', 'update:remember', 'blur', 'focus'])

const showPassword = ref(false)
const rememberValue = ref(props.remember)

// Usar VeeValidate se houver um nome de campo definido
const fieldName = computed(() => props.name || props.id)

// Configurar VeeValidate field apenas se houver rules ou se estiver dentro de um Form
let field = null
let fieldValue = computed(() => props.modelValue)
let fieldProps = computed(() => ({}))
let errorMessage = computed(() => '')
let meta = computed(() => ({ touched: false, valid: true }))

// Tentar configurar VeeValidate (falhará silenciosamente se não estiver em um contexto de Form)
try {
  if (props.rules || fieldName.value) {
    const veeField = useField(fieldName.value, props.rules, {
      validateOnValueUpdate: props.validateOnInput,
      initialValue: props.modelValue
    })
    
    field = veeField
    fieldValue = veeField.value
    errorMessage = veeField.errorMessage
    meta = veeField.meta
    
    fieldProps = computed(() => ({
      onBlur: veeField.handleBlur,
    }))
  }
} catch (error) {
  // Se VeeValidate não estiver disponível, usar valores padrão
  console.debug('VeeValidate não disponível para este campo:', fieldName.value, error.message)
}

// ID único para o input
const inputId = computed(() => props.id)

// Controle de visibilidade da senha
const passwordType = computed(() => showPassword.value ? 'text' : 'password')
const passwordIcon = computed(() => showPassword.value ? 'ri-eye-line' : 'ri-eye-off-line')

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// Erro a ser exibido (prioriza VeeValidate, depois custom)
const displayError = computed(() => {
  return errorMessage.value || props.error
})

// Manipulação de input
const handleInput = (event) => {
  const value = event.target.value
  
  // Atualizar VeeValidate se disponível
  if (field) {
    fieldValue.value = value
  }
  
  // Emitir para v-model
  emit('update:modelValue', value)
}

// Manipulação de blur
const handleBlur = (event) => {
  // Chamar handler do VeeValidate se disponível
  if (field && props.validateOnBlur) {
    field.handleBlur(event)
  }
  
  // Emitir evento
  emit('blur', event)
}

const inputClasses = computed(() => {
  const baseClasses = 'form-control w-full !border-s border-inputborder !rounded-e-none bg-defaultbackground text-defaulttextcolor'
  const sizeClasses = {
    sm: 'form-control-sm',
    md: 'form-control',
    lg: 'form-control-lg'
  }
  
  // Classes de validação
  let validationClasses = ''
  if (meta.value && meta.value.touched) {
    if (displayError.value) {
      validationClasses = 'border-danger'
    } else if (meta.value.valid && fieldValue.value) {
      validationClasses = 'border-success'
    }
  } else if (displayError.value) {
    validationClasses = 'border-danger'
  }
  
  const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : ''
  
  return [
    baseClasses,
    sizeClasses[props.size],
    validationClasses,
    disabledClasses
  ].filter(Boolean).join(' ')
})

// Exposer métodos para acesso externo
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
  fieldValue,
  errorMessage: displayError,
  meta,
  validate: field?.validate,
  togglePassword
})
</script>
