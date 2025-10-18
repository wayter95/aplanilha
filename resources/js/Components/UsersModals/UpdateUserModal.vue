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
            Editar Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="updateUser">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nome
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="ti-form-input"
                placeholder="Digite o nome do usuário"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Email
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                class="ti-form-input"
                placeholder="Digite o email do usuário"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nova Senha (deixe em branco para manter a atual)
              </label>
              <input
                v-model="form.password"
                type="password"
                class="ti-form-input"
                placeholder="Digite a nova senha"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Função
              </label>
              <select v-model="form.role" required class="ti-form-select">
                <option value="">Selecione uma função</option>
                <option value="admin">Administrador</option>
                <option value="user">Usuário</option>
                <option value="moderator">Moderador</option>
                <option value="guest">Convidado</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status
              </label>
              <select v-model="form.status" required class="ti-form-select">
                <option value="">Selecione um status</option>
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
              </select>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-6">
            <button
              type="button"
              @click="close"
              class="ti-btn btn-wave ti-btn-outline-default"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="ti-btn btn-wave ti-btn-primary"
            >
              Atualizar Usuário
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const page = usePage()

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

const emit = defineEmits(['close', 'user-updated'])

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  status: ''
})

// Watch for user changes to populate form
watch(() => props.user, (newUser) => {
  if (newUser) {
    console.log('User data:', newUser) // Debug log
    form.value = {
      name: newUser.name || '',
      email: newUser.email || '',
      password: '',
      role: newUser.role || newUser.roles?.[0]?.name || '',
      status: newUser.status || (newUser.is_active ? 'Ativo' : 'Inativo') || ''
    }
  }
}, { immediate: true })

const updateUser = async () => {
  if (!props.user || !props.user.id) {
    console.error('No user ID provided')
    return
  }

  try {
    // Prepare data with status instead of is_active
    const dataToSend = {
      ...form.value,
      // Convert status to is_active for backend
      is_active: form.value.status === 'Ativo'
    }

    const response = await fetch(`/api/users/${props.user.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(dataToSend)
    })

    const result = await response.json()

    if (result.success) {
      emit('user-updated', result.user)
      close()
    } else {
      console.error('Error updating user:', result.message)
      alert('Erro ao atualizar usuário: ' + result.message)
    }
  } catch (error) {
    console.error('Error updating user:', error)
    alert('Erro ao atualizar usuário')
  }
}

const close = () => {
  emit('close')
}
</script>
