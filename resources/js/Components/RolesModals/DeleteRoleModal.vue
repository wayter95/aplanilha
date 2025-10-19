<template>
  <Modal :show="show" @close="closeModal" max-width="sm">
    <div class="box-header">
      <h6 class="box-title">Confirmar Exclusão</h6>
      <button @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <i class="ri-close-line text-lg"></i>
      </button>
    </div>
    <div class="box-body">
      <p class="text-defaulttextcolor dark:text-defaulttextcolor/70 mb-4">
        Tem certeza que deseja excluir a função <span class="font-semibold">{{ role ? role.display_name : '' }}</span>?
        Esta ação não pode ser desfeita.
      </p>
      
      <div v-if="role" class="bg-light dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-warning text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">
            <i class="bx bx-shield"></i>
          </div>
          <div>
            <div class="leading-none">
              <span class="font-semibold">{{ role.display_name }}</span>
            </div>
            <div class="leading-none">
              <span class="text-[0.6875rem] text-textmuted dark:text-textmuted">
                {{ role.name }}
              </span>
            </div>
            <div v-if="role.description" class="leading-none mt-1">
              <span class="text-[0.6875rem] text-textmuted dark:text-textmuted">
                {{ role.description }}
              </span>
            </div>
          </div>
        </div>
        
        <div v-if="role.permissions && role.permissions.length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
          <div class="text-xs text-textmuted dark:text-textmuted mb-2">
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
    </div>
    <div class="box-footer text-end">
      <button type="button" @click="closeModal" class="ti-btn ti-btn-light me-2">Cancelar</button>
      <button type="button" @click="deleteRole" class="ti-btn ti-btn-danger" :disabled="processing">
        <span v-if="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Excluir
      </button>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
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
