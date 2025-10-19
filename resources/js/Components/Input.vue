<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="id" 
      class="form-label text-defaulttextcolor"
    >
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    
    <input 
      :type="type"
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
    
    <p v-if="error" class="text-danger text-xs mt-1">{{ error }}</p>
    <p v-if="help" class="text-textmuted text-xs mt-1">{{ help }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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
    default: ''
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
  }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

const inputClasses = computed(() => {
  const baseClasses = 'form-control w-full !rounded-md bg-defaultbackground border-inputborder text-defaulttextcolor'
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
