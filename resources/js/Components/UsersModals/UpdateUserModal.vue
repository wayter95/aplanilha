<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <!-- Modal -->
      <div
        class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"
      >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Editar Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- ✅ BaseForm para validação -->
        <BaseForm @submit="updateUser">
          <div class="space-y-4">
            <Input
              id="update-user-name"
              name="name"
              v-model="form.name"
              type="text"
              label="Nome"
              placeholder="Digite o nome do usuário"
              required
              :rules="nameRules"
            />

            <Input
              id="update-user-email"
              name="email"
              v-model="form.email"
              type="email"
              label="E-mail"
              placeholder="Digite o e-mail do usuário"
              required
              :rules="emailRules"
            />

            <InputPassword
              id="update-user-password"
              name="password"
              v-model="form.password"
              label="Nova Senha"
              placeholder="Digite uma nova senha"
              help="Deixe em branco para manter a atual"
              :rules="passwordRules"
            />

            <!-- Função -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Função
              </label>
              <select
                v-model="form.role"
                name="role"
                required
                class="ti-form-select w-full"
              >
                <option value="">Selecione uma função</option>
                <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                  {{ role.display_name }}
                </option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status
              </label>
              <select
                v-model="form.status"
                name="status"
                required
                class="ti-form-select w-full"
              >
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
              :disabled="loading"
              class="ti-btn btn-wave ti-btn-primary"
            >
              <span v-if="loading" class="flex items-center">
                <i class="ri-loader-4-line animate-spin mr-2"></i>
                Atualizando...
              </span>
              <span v-else>
                Atualizar Usuário
              </span>
            </button>
          </div>
        </BaseForm>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseForm from '@/Components/Form/BaseForm.vue'
import Input from '@/Components/Input.vue'
import InputPassword from '@/Components/InputPassword.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const { success, error } = useToast()
const page = usePage()
const loading = ref(false)

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  },
  availableRoles: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'user-updated'])

// 📋 Regras de validação
const nameRules = 'required'
const emailRules = 'required|email'
const passwordRules = 'min:8'

// 📌 Estado do formulário
const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  status: ''
})

// 🧭 Buscar ID da função pelo nome
const getRoleIdByName = (roleName) => {
  const role = props.availableRoles.find(r => r.name === roleName)
  return role ? role.id : null
}

// 🕵️‍♂️ Preencher dados ao abrir modal
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.value = {
      name: newUser.name || '',
      email: newUser.email || '',
      password: '',
      role: newUser.role || newUser.roles?.[0]?.name || '',
      status: newUser.status || (newUser.is_active ? 'Ativo' : 'Inativo') || ''
    }
  }
}, { immediate: true })

// 🚀 Atualizar usuário
const updateUser = async (values, { setErrors }) => {
  if (!props.user || !props.user.id) {
    console.error('Nenhum ID de usuário fornecido')
    return
  }

  loading.value = true

  try {
    const roleId = getRoleIdByName(values.role)
    const dataToSend = {
      ...form.value,
      role_id: roleId,
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
      success('Usuário atualizado com sucesso!')
      emit('user-updated', result.user)
      close()
    } else {
      if (result.errors) {
        setErrors(result.errors)
      } else {
        error(result.message || 'Erro ao atualizar usuário.')
      }
    }
  } catch (err) {
    console.error('Erro ao atualizar usuário:', err)
    error('Erro ao atualizar usuário')
  } finally {
    loading.value = false
  }
}

const close = () => {
  emit('close')
}
</script>
