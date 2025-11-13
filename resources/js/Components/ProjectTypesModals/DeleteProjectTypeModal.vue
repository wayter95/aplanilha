<template>
  <Modal
    :show="show"
    title="Excluir Tipo de Projeto"
    description="Esta ação não pode ser desfeita."
    icon="ri-delete-bin-line"
    type="danger"
    size="md"
    @close="closeModal"
  >
    <div v-if="type" class="bg-light dark:bg-gray-700 rounded-lg p-4 mb-4">
      <div class="flex items-center">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mr-3" :style="{ backgroundColor: type.color }">
          <i class="ri-folder-line text-white"></i>
        </div>
        <div>
          <div class="font-medium text-gray-900 dark:text-white">{{ type.title }}</div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            <span :class="['badge', type.status === 'a' ? 'bg-primary' : 'bg-secondary']">
              {{ type.status === 'a' ? 'Ativo' : 'Bloqueado' }}
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <p class="text-sm text-gray-600 dark:text-gray-300">
      O tipo de projeto <strong>{{ type?.title }}</strong> será permanentemente excluído do sistema.
    </p>

    <template #footer>
      <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
      <button type="button" @click="deleteType" class="ti-btn ti-btn-danger ml-2" :disabled="processing">
        <span v-if="processing" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
        Excluir
      </button>
    </template>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()
const { success, error } = useToast()

const props = defineProps({
  show: Boolean,
  type: Object,
})

const emit = defineEmits(['close', 'type-deleted'])

const processing = ref(false)

const deleteType = async () => {
  processing.value = true
  if (props.type && props.type.id) {
    try {
      const response = await fetch(`/api/project-types/${props.type.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': page.props.csrf_token
        }
      })

      const result = await response.json()

      if (result.success) {
        success(result.message || 'Tipo de projeto excluído com sucesso!')
        emit('type-deleted')
        closeModal()
      } else {
        error(result.message || 'Erro ao excluir tipo de projeto')
      }
    } catch (err) {
      console.error('Error deleting type:', err)
      error('Erro ao excluir tipo de projeto')
    } finally {
      processing.value = false
    }
  }
}

const closeModal = () => {
  emit('close')
}
</script>
