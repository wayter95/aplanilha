<template>
  <Transition
    name="toast"
    enter-active-class="transition-all duration-500 ease-out"
    enter-from-class="transform translate-x-full opacity-0 scale-95"
    enter-to-class="transform translate-x-0 opacity-100 scale-100"
    leave-active-class="transition-all duration-400 ease-in"
    leave-from-class="transform translate-x-0 opacity-100 scale-100"
    leave-to-class="transform translate-x-full opacity-0 scale-95"
  >
    <div
      v-if="visible"
      :class="[
        'alert flex items-center mb-3 min-w-[300px] max-w-[400px] shadow-lg cursor-pointer',
        toast.class
      ]"
      role="alert"
      @mouseenter="onMouseEnter"
      @mouseleave="onMouseLeave"
      @click="onClick"
    >
      <!-- Ícone do Toast -->
      <div class="sm:flex-shrink-0 me-2">
        <!-- Ícone de Sucesso -->
        <svg v-if="toast.icon === 'success'" class="text-white" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="currentColor">
          <path d="M0 0h24v24H0V0zm0 0h24v24H0V0z" fill="none" />
          <path d="M16.59 7.58L10 14.17l-3.59-3.58L5 12l5 5 8-8zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
        </svg>

        <!-- Ícone de Erro/Danger -->
        <svg v-else-if="toast.icon === 'danger'" class="text-white" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="currentColor">
          <g>
            <rect fill="none" height="24" width="24" />
          </g>
          <g>
            <g>
              <g>
                <path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z" />
                <rect height="6" width="2" x="11" y="7" />
                <rect height="2" width="2" x="11" y="15" />
              </g>
            </g>
          </g>
        </svg>

        <!-- Ícone de Warning -->
        <svg v-else-if="toast.icon === 'warning'" class="text-white" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="currentColor">
          <g>
            <rect fill="none" height="24" width="24" />
          </g>
          <g>
            <g>
              <g>
                <path d="M12,5.99L19.53,19H4.47L12,5.99 M12,2L1,21h22L12,2L12,2z" />
                <polygon points="13,16 11,16 11,18 13,18" />
                <polygon points="13,10 11,10 11,15 13,15" />
              </g>
            </g>
          </g>
        </svg>

        <!-- Ícone de Info/Primary -->
        <svg v-else class="text-white" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="currentColor">
          <path d="M0 0h24v24H0V0z" fill="none" />
          <path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
        </svg>
      </div>

      <!-- Conteúdo do Toast -->
      <div class="flex-1">
        <div class="text-sm font-medium">
          {{ toast.message }}
        </div>
      </div>

      <!-- Botão de Fechar -->
      <div class="ms-auto">
        <div class="mx-1 my-1">
          <button
            v-if="toast.config.closable"
            @click.stop="onClose"
            class="inline-flex rounded-sm focus:outline-none focus:ring-0 focus:ring-offset-0 cursor-pointer z-10 relative text-white hover:text-gray-200"
            type="button"
          >
            <span class="sr-only">Dismiss</span>
            <svg class="h-3 w-3" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M0.92524 0.687069C1.126 0.486219 1.39823 0.373377 1.68209 0.373377C1.96597 0.373377 2.2382 0.486219 2.43894 0.687069L8.10514 6.35813L13.7714 0.687069C13.8701 0.584748 13.9882 0.503105 14.1188 0.446962C14.2494 0.39082 14.3899 0.361248 14.5321 0.360026C14.6742 0.358783 14.8151 0.38589 14.9468 0.439762C15.0782 0.493633 15.1977 0.573197 15.2983 0.673783C15.3987 0.774389 15.4784 0.894026 15.5321 1.02568C15.5859 1.15736 15.6131 1.29845 15.6118 1.44071C15.6105 1.58297 15.5809 1.72357 15.5248 1.85428C15.4688 1.98499 15.3872 2.10324 15.2851 2.20206L9.61883 7.87312L15.2851 13.5441C15.4801 13.7462 15.588 14.0168 15.5854 14.2977C15.5831 14.5787 15.4705 14.8474 15.272 15.046C15.0735 15.2449 14.805 15.3574 14.5244 15.3599C14.2437 15.3623 13.9733 15.2543 13.7714 15.0591L8.10514 9.38812L2.43894 15.0591C2.23704 15.2543 1.96663 15.3623 1.68594 15.3599C1.40526 15.3574 1.13677 15.2449 0.938279 15.046C0.739807 14.8474 0.627232 14.5787 0.624791 14.2977C0.62235 14.0168 0.730236 13.7462 0.92524 13.5441L6.59144 7.87312L0.92524 2.20206C0.724562 2.00115 0.611816 1.72867 0.611816 1.44457C0.611816 1.16047 0.724562 0.887983 0.92524 0.687069Z" fill="currentColor" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  toast: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'pause', 'resume'])

const visible = computed(() => props.toast.visible)

const onMouseEnter = () => {
  if (props.toast.config.pauseOnHover) {
    emit('pause', props.toast.id)
  }
}

const onMouseLeave = () => {
  if (props.toast.config.pauseOnHover) {
    emit('resume', props.toast.id)
  }
}

const onClick = () => {
  // Pode ser usado para ações customizadas
}

const onClose = (event) => {
  event.preventDefault()
  event.stopPropagation()
  emit('close', props.toast.id)
}
</script>

<style scoped>
/* Animações melhoradas para toast */
.toast-enter-active {
  transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 1, 1);
}

.toast-enter-from {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

/* Efeito de hover suave */
.alert {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.alert:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Animação do botão de fechar */
.alert button {
  transition: all 0.2s ease;
}

.alert button:hover {
  transform: scale(1.1);
}

/* Efeito de pulse para toasts de sucesso */
.alert-solid-success {
  animation: successPulse 0.6s ease-out;
}

@keyframes successPulse {
  0% {
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
  }
}

/* Efeito de shake para toasts de erro */
.alert-solid-danger {
  animation: errorShake 0.6s ease-out;
}

@keyframes errorShake {
  0%, 100% {
    transform: translateX(0);
  }
  10%, 30%, 50%, 70%, 90% {
    transform: translateX(-2px);
  }
  20%, 40%, 60%, 80% {
    transform: translateX(2px);
  }
}

/* Efeito de bounce para toasts de warning */
.alert-solid-warning {
  animation: warningBounce 0.6s ease-out;
}

@keyframes warningBounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translateY(0);
  }
  40%, 43% {
    transform: translateY(-8px);
  }
  70% {
    transform: translateY(-4px);
  }
  90% {
    transform: translateY(-2px);
  }
}

/* Efeito de fade para toasts de info */
.alert-solid-info {
  animation: infoFade 0.6s ease-out;
}

@keyframes infoFade {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
