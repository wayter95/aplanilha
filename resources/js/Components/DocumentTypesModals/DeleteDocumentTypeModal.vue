<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Excluir Tipo de Documento
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- Content -->
        <div class="mb-6">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-4">
              <i class="ri-delete-bin-line text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <div>
              <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                Tem certeza?
              </h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Esta ação não pode ser desfeita.
              </p>
            </div>
          </div>
      
          <div v-if="type" class="bg-light dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">
                <i class="ri-file-list-line"></i>
              </div>
              <div>
                <div class="font-medium text-gray-900 dark:text-white">{{ type.name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  <code class="text-xs bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded">{{ type.code }}</code>
                </div>
                <div v-if="type.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  {{ type.description }}
                </div>
              </div>
            </div>
          </div>
          
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-4">
            O tipo de documento <strong>{{ type?.name }}</strong> será permanentemente excluído do sistema.
          </p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
          <button type="button" @click="deleteType" class="ti-btn ti-btn-danger" :disabled="processing">
            <span v-if="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
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
      const response = await fetch(`/document-types/${props.type.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': page.props.csrf_token
        }
      })

      const result = await response.json()

      if (response.ok) {
        success('Tipo de documento excluído com sucesso!')
        emit('type-deleted')
        closeModal()
      } else {
        console.error('Error deleting type:', result.message)
        error(result.message || 'Erro ao excluir tipo de documento')
      }
    } catch (err) {
      console.error('Error deleting type:', err)
      error('Erro ao excluir tipo de documento')
    } finally {
      processing.value = false
    }
  }
}

const closeModal = () => {
  emit('close')
}
</script>

