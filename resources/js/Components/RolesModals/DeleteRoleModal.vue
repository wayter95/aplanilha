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
            Excluir Função
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
      
          <div v-if="role" class="bg-light dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-warning text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">
                <i class="bx bx-shield"></i>
              </div>
              <div>
                <div class="font-medium text-gray-900 dark:text-white">{{ role.display_name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ role.name }}</div>
                <div v-if="role.description" class="text-sm text-gray-500 dark:text-gray-400">
                  {{ role.description }}
                </div>
              </div>
            </div>
            
            <div v-if="role.permissions && role.permissions.length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                Permissões associadas ({{ role.permissions.length }}):
              </div>
              <div class="flex flex-wrap gap-1">
                <span 
                  v-for="permission in role.permissions.slice(0, 3)" 
                  :key="permission.id"
                  class="text-xs bg-primary/10 text-primary px-2 py-1 rounded"
                >
                  {{ `${permission.module} - ${permission.action}` }}
                </span>
                <span 
                  v-if="role.permissions.length > 3"
                  class="text-xs bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 px-2 py-1 rounded"
                >
                  +{{ role.permissions.length - 3 }} mais
                </span>
              </div>
            </div>
          </div>
          
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-4">
            A função <strong>{{ role?.display_name }}</strong> será permanentemente excluída do sistema.
          </p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
          <button type="button" @click="deleteRole" class="ti-btn ti-btn-danger" :disabled="processing">
            <span v-if="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()

const props = defineProps({
  show: Boolean,
  role: Object,
})

const emit = defineEmits(['close', 'role-deleted'])

const processing = ref(false)

const deleteRole = async () => {
  processing.value = true
  if (props.role && props.role.id) {
    try {
      const response = await fetch(`/api/roles/${props.role.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': page.props.csrf_token
        }
      })

      const result = await response.json()

      if (result.success) {
        emit('role-deleted')
        closeModal()
      } else {
        console.error('Error deleting role:', result.message)
        alert('Erro ao excluir função: ' + result.message)
      }
    } catch (error) {
      console.error('Error deleting role:', error)
      alert('Erro ao excluir função')
    } finally {
      processing.value = false
    }
  }
}

const closeModal = () => {
  emit('close')
}
</script>
