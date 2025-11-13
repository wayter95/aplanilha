<template>
  <Modal
    :show="show"
    title="Alterações não salvas"
    icon="ri-alert-line"
    type="warning"
    size="md"
    @close="cancel"
  >
    <!-- Body -->
    <div class="space-y-3">
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Você possui alterações não salvas nessa aba. 
        <span class="font-semibold text-gray-900 dark:text-white">
          Deseja realmente fechar e perder as informações?
        </span>
      </p>
      
      <div 
        v-if="tabTitle" 
        class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600"
      >
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-0">
          <strong class="text-gray-900 dark:text-white">Aba:</strong> {{ tabTitle }}
        </p>
      </div>
    </div>

    <!-- Footer com botões -->
    <template #footer>
      <button
        type="button"
        class="ti-btn ti-btn-light"
        @click="cancel"
      >
        <i class="ri-close-line mr-1"></i>
        Cancelar
      </button>
      <button
        type="button"
        class="ti-btn ti-btn-danger-full"
        @click="confirm"
      >
        <i class="ri-delete-bin-line mr-1"></i>
        Descartar e Fechar
      </button>
    </template>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  tabTitle: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
}
</script>
