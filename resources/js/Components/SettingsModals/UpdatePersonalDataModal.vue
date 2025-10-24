<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Atualizar Dados Pessoais
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="updatePersonalData">
          <div class="space-y-4">
            <Input
              id="update-name"
              v-model="form.name"
              type="text"
              label="Nome"
              placeholder="Digite seu nome completo"
              required
            />

            <Input
              id="update-username"
              v-model="form.username"
              type="text"
              label="Usuário"
              placeholder="Digite seu nome de usuário"
            />

            <Input
              id="update-email"
              v-model="form.email"
              type="email"
              label="E-mail"
              placeholder="Digite seu e-mail"
              required
            />

            <Input
              id="update-phone"
              v-model="form.phone"
              type="tel"
              label="Telefone"
              placeholder="Digite seu telefone"
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
                Salvando...
              </span>
              <span v-else>Salvar Alterações</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import Input from '@/Components/Input.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

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

const emit = defineEmits(['close', 'personal-data-updated'])

const { success, error } = useToast()
const page = usePage()

const loading = ref(false)

const form = ref({
  name: '',
  username: '',
  email: '',
  phone: ''
})

// Preencher formulário quando o modal abrir
watch(() => props.show, (newValue) => {
  if (newValue && props.user) {
    form.value = {
      name: props.user.name || '',
      username: props.user.username || '',
      email: props.user.email || '',
      phone: props.user.phone || ''
    }
  }
})

const close = () => {
  emit('close')
}

const updatePersonalData = async () => {
  loading.value = true
  
  try {
    const response = await fetch('/api/user/personal-data', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (data.success) {
      success('Dados pessoais atualizados com sucesso!')
      emit('personal-data-updated', data.user)
      close()
    } else {
      error(data.message || 'Erro ao atualizar dados pessoais')
    }
  } catch (err) {
    error('Erro ao atualizar dados pessoais')
    console.error('Erro:', err)
  } finally {
    loading.value = false
  }
}
</script>
