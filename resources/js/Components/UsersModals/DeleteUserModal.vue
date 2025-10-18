<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <!-- Modal -->
      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Excluir Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
          
          <div v-if="user" class="bg-light dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">
                {{ getInitials(user.name) }}
              </div>
              <div>
                <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
              </div>
            </div>
          </div>
          
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-4">
            O usuário <strong>{{ user?.name }}</strong> será permanentemente excluído do sistema.
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <button
            type="button"
            @click="close"
            class="ti-btn btn-wave ti-btn-outline-default"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="deleteUser"
            class="ti-btn btn-wave ti-btn-danger"
          >
            Excluir Usuário
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'user-deleted'])

const getInitials = (name) => {
  if (!name) return ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const deleteUser = async () => {
  if (!props.user || !props.user.id) {
    console.error('No user ID provided')
    return
  }

  try {
    const response = await fetch(`/api/users/${props.user.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    const result = await response.json()

    if (result.success) {
      emit('user-deleted', props.user)
      close()
    } else {
      console.error('Error deleting user:', result.message)
      alert('Erro ao excluir usuário: ' + result.message)
    }
  } catch (error) {
    console.error('Error deleting user:', error)
    alert('Erro ao excluir usuário')
  }
}

const close = () => {
  emit('close')
}
</script>
