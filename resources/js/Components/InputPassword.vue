<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="id" 
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
        :id="id"
        :name="name"
        :placeholder="placeholder"
        :value="modelValue"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
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
    
    <div v-if="showRemember" class="mt-2">
      <div class="form-check !ps-0">
        <input 
          class="form-check-input" 
          type="checkbox" 
          :id="`remember-${id}`"
          v-model="rememberValue"
          @change="$emit('update:remember', $event.target.checked)"
        >
        <label 
          class="form-check-label text-textmuted font-normal" 
          :for="`remember-${id}`"
        >
          Lembrar senha?
        </label>
      </div>
    </div>
    
    <p v-if="error" class="text-danger text-xs mt-1">{{ error }}</p>
    <p v-if="help" class="text-textmuted text-xs mt-1">{{ help }}</p>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

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
  }
})

const emit = defineEmits(['update:modelValue', 'update:remember', 'blur', 'focus'])

const showPassword = ref(false)
const rememberValue = ref(props.remember)

const passwordType = computed(() => showPassword.value ? 'text' : 'password')
const passwordIcon = computed(() => showPassword.value ? 'ri-eye-line' : 'ri-eye-off-line')

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const inputClasses = computed(() => {
  const baseClasses = 'form-control w-full !border-s border-inputborder !rounded-e-none bg-defaultbackground text-defaulttextcolor'
  const sizeClasses = {
    sm: 'form-control-sm',
    md: 'form-control',
    lg: 'form-control-lg'
  }
  const errorClasses = props.error ? 'border-danger' : ''
  const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : ''
  
  return [
    baseClasses,
    sizeClasses[props.size],
    errorClasses,
    disabledClasses
  ].filter(Boolean).join(' ')
})
</script>
