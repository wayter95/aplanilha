<template>
  <div class="space-y-2">
    <label v-if="label" class="ti-form-label">{{ label }}</label>
    <div class="flex items-center gap-1">
      <input
        type="color"
        :value="modelValue"
        @input="handleInput"
        class="ti-form-input form-input-color !rounded-md"
        :disabled="disabled"
      />
      <FormInput
        name="color-text"
        :model-value="modelValue"
        @update:model-value="handleTextInput"
        @blur="validateColor"
        placeholder="#000000"
        :disabled="disabled"
        :show-validation="false"
      />
    </div>
    <p v-if="hint" class="text-sm text-gray-600 dark:text-white/70">{{ hint }}</p>
    
    <!-- Cores predefinidas do Ynex -->
    <div v-if="showPresets" class="flex flex-wrap gap-2 mt-2">
      <Button
        v-for="color in presetColors"
        :key="color"
        tag="button"
        :custom-class="`inline-block size-8 rounded-md border-2 border-defaultborder hover:scale-110 transition-transform ${modelValue === color ? '!border-primary !border-4' : ''}`"
        :style="{ backgroundColor: color }"
        @click="selectColor(color)"
        :disabled="disabled"
        :title="color"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import FormInput from '@/Components/Form/FormInput.vue'
import Button from '@/Components/Button.vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '#000000'
  },
  label: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showPresets: {
    type: Boolean,
    default: true
  },
  presetColors: {
    type: Array,
    default: () => [
      '#845adf', // Primary
      '#f8b739', // Secondary
      '#22c55e', // Success
      '#ef4444', // Danger
      '#f59e0b', // Warning
      '#0ea5e9', // Info
      '#64748b', // Light
      '#1e293b', // Dark
      '#7c3aed', // Primary 600
      '#d97706', // Warning 600
      '#16a34a', // Success 600
      '#dc2626'  // Danger 600
    ]
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const handleInput = (event) => {
  const color = event.target.value
  emit('update:modelValue', color)
  emit('change', color)
}

const handleTextInput = (value) => {
  let color = value.trim()
  if (color && !color.startsWith('#')) {
    color = '#' + color
  }
  emit('update:modelValue', color)
}

const validateColor = () => {
  let color = props.modelValue.trim()
  const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/
  
  if (!color.startsWith('#')) {
    color = '#' + color
  }
  
  if (!hexRegex.test(color)) {
    return
  }
  
  if (color.length === 4) {
    color = '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3]
  }
  
  emit('update:modelValue', color)
  emit('change', color)
}

const selectColor = (color) => {
  emit('update:modelValue', color)
  emit('change', color)
}
</script>
