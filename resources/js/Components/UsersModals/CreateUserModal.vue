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
            Novo Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="createUser">
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
                Senha
              </label>
              <input
                v-model="form.password"
                type="password"
                required
                class="ti-form-input"
                placeholder="Digite a senha"
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
              Criar Usuário
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'user-created'])

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  status: ''
})

const createUser = async () => {
  try {
    // Prepare data with status instead of is_active
    const dataToSend = {
      ...form.value,
      // Convert status to is_active for backend
      is_active: form.value.status === 'Ativo'
    }

    const response = await fetch('/api/users', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(dataToSend)
    })

    const result = await response.json()

    if (result.success) {
      emit('user-created', result.user)
      close()
    } else {
      console.error('Error creating user:', result.message)
      alert('Erro ao criar usuário: ' + result.message)
    }
  } catch (error) {
    console.error('Error creating user:', error)
    alert('Erro ao criar usuário')
  }
}

const close = () => {
  emit('close')
}
</script>
