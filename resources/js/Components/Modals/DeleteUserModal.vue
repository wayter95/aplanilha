<template>
  <Modal
    :show="show"
    title="Confirmar Exclusão"
    description="Esta ação não pode ser desfeita"
    icon="bx bx-trash"
    type="danger"
    size="sm"
    @close="$emit('close')"
  >
    <div class="text-center">
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
        <i class="bx bx-trash text-red-600 dark:text-red-400 text-2xl"></i>
      </div>
      
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        Excluir Usuário
      </h3>
      
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Tem certeza que deseja excluir o usuário <strong>{{ user?.name }}</strong>?
      </p>
      
      <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <i class="bx bx-error-circle text-red-400 text-xl"></i>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
              Atenção!
            </h3>
            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
              <p>Esta ação irá:</p>
              <ul class="list-disc list-inside mt-1 space-y-1">
                <li>Excluir permanentemente o usuário</li>
                <li>Remover todos os dados associados</li>
                <li>Cancelar qualquer sessão ativa</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- User Info -->
      <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-left">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Informações do Usuário</h4>
        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
          <p><strong>Nome:</strong> {{ user?.name }}</p>
          <p><strong>Email:</strong> {{ user?.email }}</p>
          <p><strong>Tipo:</strong> {{ user?.is_master ? 'Administrador' : 'Usuário' }}</p>
          <p><strong>Status:</strong> {{ user?.status || 'Ativo' }}</p>
          <p><strong>Criado em:</strong> {{ formatDate(user?.created_at) }}</p>
        </div>
      </div>
    </div>

    <template #footer>
      <button
        type="button"
        @click="$emit('close')"
        class="btn btn-outline-primary mr-3"
      >
        Cancelar
      </button>
      <button
        type="button"
        @click="deleteUser"
        :disabled="processing"
        class="btn btn-danger"
        :class="{ 'opacity-50 cursor-not-allowed': processing }"
      >
        <i v-if="processing" class="bx bx-loader-alt animate-spin mr-1"></i>
        <i v-else class="bx bx-trash mr-1"></i>
        {{ processing ? 'Excluindo...' : 'Excluir Usuário' }}
      </button>
    </template>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'success'])

const processing = ref(false)

const deleteUser = () => {
  if (props.user?.id) {
    processing.value = true
    
    router.delete(route('users.destroy', props.user.id), {
      onSuccess: () => {
        emit('success')
        emit('close')
      },
      onFinish: () => {
        processing.value = false
      }
    })
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('pt-BR')
}
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease-in-out;
  outline: none;
}

.btn:focus {
  ring: 2px;
  ring-offset: 2px;
}

.btn-primary {
  background-color: #2563eb;
  color: white;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-primary:focus {
  ring-color: #3b82f6;
}

.btn-outline-primary {
  border-color: #93c5fd;
  color: #1d4ed8;
  background-color: #eff6ff;
}

.btn-outline-primary:hover {
  background-color: #dbeafe;
}

.dark .btn-outline-primary {
  border-color: #2563eb;
  color: #60a5fa;
  background-color: rgba(30, 58, 138, 0.2);
}

.dark .btn-outline-primary:hover {
  background-color: rgba(30, 58, 138, 0.3);
}

.btn-danger {
  background-color: #dc2626;
  color: white;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

.btn-danger:focus {
  ring-color: #ef4444;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
