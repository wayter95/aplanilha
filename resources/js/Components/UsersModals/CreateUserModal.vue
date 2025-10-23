<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <div
        class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Novo Usuário
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- ✅ Adicionado BaseForm para validação -->
        <BaseForm @submit="createUser">
          <div class="space-y-4">
            <div>
              <Input
                id="create-user-name"
                name="name"
                v-model="form.name"
                type="text"
                label="Nome"
                placeholder="Digite o nome do usuário"
                required
                :rules="nameRules"
              />
              <p v-if="errors.name" class="text-danger text-xs mt-1">{{ errors.name }}</p>
            </div>

            <div>
              <Input
                id="create-user-email"
                name="email"
                v-model="form.email"
                type="email"
                label="E-mail"
                placeholder="Digite o e-mail do usuário"
                required
                :rules="emailRules"
              />
              <p v-if="errors.email" class="text-danger text-xs mt-1">{{ errors.email }}</p>
            </div>

            <div>
              <InputPassword
                id="create-user-password"
                name="password"
                v-model="form.password"
                label="Senha"
                placeholder="Digite a senha"
                required
                :rules="passwordRules"
              />
              <p v-if="errors.password" class="text-danger text-xs mt-1">{{ errors.password }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Função
              </label>
              <select
                v-model="form.role"
                name="role"
                required
                class="ti-form-select"
                :class="{'border-danger': errors.role}"
                v-validate
                :rules="roleRules"
              >
                <option value="">Selecione uma função</option>
                <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                  {{ role.display_name }}
                </option>
              </select>
              <p v-if="errors.role" class="text-danger text-xs mt-1">{{ errors.role }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status
              </label>
              <select
                v-model="form.status"
                name="status"
                required
                class="ti-form-select"
                :class="{'border-danger': errors.status}"
                v-validate
                :rules="statusRules"
              >
                <option value="">Selecione um status</option>
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
              </select>
              <p v-if="errors.status" class="text-danger text-xs mt-1">{{ errors.status }}</p>
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
              :disabled="isSubmitting"
            >
              Criar Usuário
            </button>
          </div>
        </BaseForm>
      </div>
    </div>
  </div>
</template>

<script setup>
import Input from '@/Components/Input.vue'
import InputPassword from '@/Components/InputPassword.vue'
import BaseForm from '@/Components/Form/BaseForm.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useForm } from 'vee-validate'

const page = usePage()
const { success, error } = useToast()

const props = defineProps({
  show: { type: Boolean, default: false },
  availableRoles: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'user-created'])

// 🧾 Formulário
const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  status: ''
})

// 🧭 Regras de validação
const nameRules = 'required|min:2|max:100|alpha_spaces'
const emailRules = 'required|email'
const passwordRules = 'required|min:8'
const roleRules = 'required'
const statusRules = 'required'

// 📌 vee-validate
const { errors, handleSubmit, isSubmitting } = useForm()

const getRoleIdByName = (roleName) => {
  const role = props.availableRoles.find(r => r.name === roleName)
  return role ? role.id : null
}

const createUser = handleSubmit(async () => {
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
  } catch (err) {
    console.error('Error creating user:', err)
    error('Erro ao criar usuário')
  }
})

const close = () => emit('close')
</script>
