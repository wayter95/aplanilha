<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="$emit('close')"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <!-- Modal Content -->
          <div
            :class="[
              'relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full',
              sizeClasses
            ]"
            @click.stop
          >
            <!-- Modal Header -->
            <div v-if="title || $slots.header" class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div v-if="icon" class="flex-shrink-0 mx-auto flex items-center justify-center h-12 w-12 rounded-full mr-4"
                    :class="iconClasses">
                    <i :class="icon" class="text-xl"></i>
                  </div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                    {{ title }}
                  </h3>
                </div>
                <button
                  @click="$emit('close')"
                  class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <span class="sr-only">Close</span>
                  <i class="bx bx-x text-2xl"></i>
                </button>
              </div>
              <div v-if="description" class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ description }}</p>
              </div>
              <slot name="header" />
            </div>

            <!-- Modal Body -->
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
              <slot />
            </div>

            <!-- Modal Footer -->
            <div v-if="$slots.footer" class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-600">
              <slot name="footer" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'default', // default, success, warning, danger
    validator: (value) => ['default', 'success', 'warning', 'danger'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg, xl
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  }
})

const emit = defineEmits(['close'])

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl'
  }
  return sizes[props.size] || sizes.md
})

const iconClasses = computed(() => {
  const classes = {
    default: 'bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
    success: 'bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400',
    warning: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
    danger: 'bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400'
  }
  return classes[props.type] || classes.default
})
</script>
