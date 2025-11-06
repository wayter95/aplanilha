<template>
  <Teleport to="body">
    <div
      :class="[
        'fixed z-[9999] pointer-events-none',
        positionClasses
      ]"
    >
      <div class="pointer-events-auto toast-container">
        <TransitionGroup
          name="toast-list"
          tag="div"
          class="toast-list"
        >
          <Toast
            v-for="toast in toasts"
            :key="toast.id"
            :toast="toast"
            @close="removeToast"
            @pause="pauseToast"
            @resume="resumeToast"
          />
        </TransitionGroup>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'
import { computed } from 'vue'
import Toast from './Toast.vue'

const { toasts, removeToast, pauseToast, resumeToast } = useToast()

const props = defineProps({
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => [
      'top-left',
      'top-center', 
      'top-right',
      'bottom-left',
      'bottom-center',
      'bottom-right'
    ].includes(value)
  }
})

const positionClasses = computed(() => {
  const positions = {
    'top-left': 'top-4 left-4',
    'top-center': 'top-4 left-1/2 transform -translate-x-1/2',
    'top-right': 'top-4 right-4',
    'bottom-left': 'bottom-4 left-4',
    'bottom-center': 'bottom-4 left-1/2 transform -translate-x-1/2',
    'bottom-right': 'bottom-4 right-4'
  }
  
  return positions[props.position] || positions['top-right']
})
</script>

<style scoped>
/* Container de toasts */
.toast-container {
  max-height: 100vh;
  overflow: hidden;
  pointer-events: auto;
}

.toast-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

/* Animações para lista de toasts */
.toast-list-enter-active {
  transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-list-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 1, 1);
}

.toast-list-enter-from {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

.toast-list-leave-to {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

/* Animação de movimento para reorganização */
.toast-list-move {
  transition: transform 0.3s ease;
}

/* Efeito de empilhamento para múltiplos toasts */
.toast-list .alert {
  position: relative;
}

.toast-list .alert:nth-child(1) {
  z-index: 10;
}

.toast-list .alert:nth-child(2) {
  z-index: 9;
  transform: scale(0.98);
}

.toast-list .alert:nth-child(3) {
  z-index: 8;
  transform: scale(0.96);
}

.toast-list .alert:nth-child(n+4) {
  z-index: 7;
  transform: scale(0.94);
}

/* Efeito de hover em toasts empilhados */
.toast-list .alert:hover {
  z-index: 15 !important;
  transform: scale(1) !important;
}
</style>
