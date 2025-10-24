<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Alterar Senha
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="updatePassword">
          <div class="space-y-4">
            <InputPassword
              id="current-password"
              v-model="form.current_password"
              label="Senha Atual"
              placeholder="Digite sua senha atual"
              required
            />

            <InputPassword
              id="new-password"
              v-model="form.password"
              label="Nova Senha"
              placeholder="Digite sua nova senha"
              required
            />

            <InputPassword
              id="confirm-password"
              v-model="form.password_confirmation"
              label="Confirmar Nova Senha"
              placeholder="Confirme sua nova senha"
              required
            />
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
              :disabled="loading"
              class="ti-btn btn-wave ti-btn-primary"
            >
              <span v-if="loading" class="flex items-center">
                <i class="ri-loader-4-line animate-spin mr-2"></i>
                Alterando...
              </span>
              <span v-else>Alterar Senha</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import InputPassword from '@/Components/InputPassword.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'password-updated'])

const { success, error } = useToast()
const page = usePage()

const loading = ref(false)

const form = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const close = () => {
  // Limpar formulário ao fechar
  form.value = {
    current_password: '',
    password: '',
    password_confirmation: ''
  }
  emit('close')
}

const updatePassword = async () => {
  // Validação básica
  if (form.value.password !== form.value.password_confirmation) {
    error('As senhas não coincidem')
    return
  }

  if (form.value.password.length < 8) {
    error('A nova senha deve ter pelo menos 8 caracteres')
    return
  }

  loading.value = true
  
  try {
    const response = await fetch('/api/user/password', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (data.success) {
      success('Senha alterada com sucesso!')
      emit('password-updated')
      close()
    } else {
      error(data.message || 'Erro ao alterar senha')
    }
  } catch (err) {
    error('Erro ao alterar senha')
    console.error('Erro:', err)
  } finally {
    loading.value = false
  }
}
</script>
