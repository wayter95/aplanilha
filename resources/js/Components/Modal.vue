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
        :class="modalClass"
        @click.self="handleBackdropClick"
      >
        <div 
          class="fixed inset-0 transition-opacity"
          :class="backdropClasses"
        ></div>
        
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div
            :class="[
              'relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full',
              sizeClasses,
              modalBoxClasses,
              modalContentClasses
            ]"
            @click.stop
          >
            <div v-if="title || $slots.header" class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div v-if="icon" class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full"
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

            <div :class="bodyClasses">
              <slot />
            </div>

            <div v-if="$slots.footer" class="bg-white dark:bg-bodybg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-defaultborder dark:border-white/10">
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
    default: 'default',
    validator: (value) => ['default', 'primary', 'success', 'warning', 'danger', 'info'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
  },
  // Posicionamento vertical
  centered: {
    type: Boolean,
    default: false
  },
  // Fullscreen
  fullscreen: {
    type: Boolean,
    default: false
  },
  // Fullscreen responsivo (ex: 'sm', 'md', 'lg')
  fullscreenBelow: {
    type: String,
    default: '',
    validator: (value) => !value || ['sm', 'md', 'lg', 'xl', '2xl'].includes(value)
  },
  // Backdrop estático (não fecha ao clicar fora)
  staticBackdrop: {
    type: Boolean,
    default: false
  },
  // Backdrop customizado
  backdropClass: {
    type: String,
    default: ''
  },
  // Scrollable
  scrollable: {
    type: Boolean,
    default: false
  },
  // Animação personalizada
  animation: {
    type: String,
    default: 'fade',
    validator: (value) => ['fade', 'slide-down', 'slide-up', 'zoom'].includes(value)
  },
  // Classe customizada para o modal
  modalClass: {
    type: String,
    default: ''
  },
  // Remover padding do body
  noPadding: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const sizeClasses = computed(() => {
  if (props.fullscreen) {
    return '!m-0 !max-w-full !w-full'
  }
  
  if (props.fullscreenBelow) {
    const breakpoint = props.fullscreenBelow
    return `max-w-full w-full !m-0 ${breakpoint}:hs-overlay-open:!mt-10 ${breakpoint}:!mt-0 ${breakpoint}:!max-w-lg ${breakpoint}:!mx-auto`
  }
  
  const sizes = {
    xs: 'sm:max-w-xs',
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg md:max-w-2xl',
    xl: 'sm:max-w-xl md:max-w-3xl',
    '2xl': 'sm:max-w-2xl md:max-w-4xl lg:max-w-5xl',
    full: '!max-w-full !w-full !m-0'
  }
  return sizes[props.size] || sizes.md
})

const modalBoxClasses = computed(() => {
  const classes = []
  
  // Animação de entrada
  if (props.animation === 'slide-down') {
    classes.push('hs-overlay-open:mt-7 mt-0')
  } else if (props.animation === 'slide-up') {
    classes.push('hs-overlay-open:!mt-7 !mt-14')
  } else {
    classes.push('hs-overlay-open:mt-7 mt-0')
  }
  
  // Centralização vertical
  if (props.centered) {
    classes.push('min-h-[calc(100%-3.5rem)] flex items-center')
  }
  
  // Scrollable
  if (props.scrollable && props.centered) {
    classes.push('h-[calc(100%-3.5rem)]')
  }
  
  classes.push('ease-out')
  
  return classes.join(' ')
})

const modalContentClasses = computed(() => {
  const classes = []
  
  if (props.fullscreen) {
    classes.push('!rounded-none')
  }
  
  if (props.fullscreenBelow) {
    const breakpoint = props.fullscreenBelow
    classes.push(`${breakpoint}:border ${breakpoint}:rounded-sm ${breakpoint}:shadow-sm dark:bg-bodybg ${breakpoint}:dark:border-white/10`)
  }
  
  return classes.join(' ')
})

const backdropClasses = computed(() => {
  if (props.backdropClass) {
    return props.backdropClass
  }
  return 'bg-gray-500 bg-opacity-75'
})

const bodyClasses = computed(() => {
  const classes = ['bg-white dark:bg-gray-800']
  
  if (props.noPadding) {
    classes.push('p-0')
  } else {
    classes.push('px-4 pt-5 pb-4 sm:p-6')
  }
  
  return classes.join(' ')
})

const iconClasses = computed(() => {
  const classes = {
    default: 'bg-gray-100 text-gray-600 dark:bg-gray-900/20 dark:text-gray-400',
    primary: 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary',
    success: 'bg-success/10 text-success dark:bg-success/20 dark:text-success',
    warning: 'bg-warning/10 text-warning dark:bg-warning/20 dark:text-warning',
    danger: 'bg-danger/10 text-danger dark:bg-danger/20 dark:text-danger',
    info: 'bg-info/10 text-info dark:bg-info/20 dark:text-info'
  }
  return classes[props.type] || classes.default
})

const handleBackdropClick = () => {
  if (!props.staticBackdrop) {
    emit('close')
  }
}
</script>
