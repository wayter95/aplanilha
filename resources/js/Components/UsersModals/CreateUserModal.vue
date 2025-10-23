<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Novo Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="createUser">
          <div class="space-y-4">
            <Input
              id="create-user-name"
              v-model="form.name"
              type="text"
              label="Nome"
              placeholder="Digite o nome do usuário"
              required
            />

            <Input
              id="create-user-email"
              v-model="form.email"
              type="email"
              label="E-mail"
              placeholder="Digite o e-mail do usuário"
              required
            />

            <InputPassword
              id="create-user-password"
              name="password"
              v-model="form.password"
              label="Senha"
              placeholder="Digite a senha"
              required
            />  
<!--  -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Função
              </label>
              <select v-model="form.role" required class="ti-form-select">
                <option value="">Selecione uma função</option>
                <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                  {{ role.display_name }}
                </option>
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
import Input from '@/Components/Input.vue'
import InputPassword from '@/Components/InputPassword.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()
const { success, error } = useToast()

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  availableRoles: {
    type: Array,
    default: () => []
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

const getRoleIdByName = (roleName) => {
  const role = props.availableRoles.find(r => r.name === roleName)
  return role ? role.id : null
}

const createUser = async () => {
  try {
    const roleId = getRoleIdByName(form.value.role)
    
    const dataToSend = {
      ...form.value,
      role_id: roleId,
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
      success('Usuário criado com sucesso!')
      emit('user-created', result.user)
      close()
    } else {
      console.error('Error creating user:', result.message)
      error('Erro ao criar usuário: ' + result.message)
    }
  } catch (error) {
    console.error('Error creating user:', error)
    error('Erro ao criar usuário')
  }
}

const close = () => {
  emit('close')
}
</script>
