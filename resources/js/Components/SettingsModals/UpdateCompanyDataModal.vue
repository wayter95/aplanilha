<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="close"></div>

      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Atualizar Dados da Empresa
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <BaseForm @submit="handleSubmit">
          <div class="space-y-4">
            <Input
              id="update-company-name"
              name="name"
              v-model="form.name"
              type="text"
              label="Nome da Empresa"
              placeholder="Digite o nome da empresa"
              required
              :rules="nameRules"
            />

            <Input
              id="update-company-cnpj"
              name="cnpj"
              v-model="form.cnpj"
              type="text"
              label="CNPJ"
              placeholder="Digite o CNPJ"
            />

            <Input
              id="update-company-email"
              name="email"
              v-model="form.email"
              type="email"
              label="E-mail Comercial"
              placeholder="Digite o e-mail comercial"
              :rules="emailRules"
            />

            <Input
              id="update-company-phone"
              name="phone"
              v-model="form.phone"
              type="tel"
              label="Telefone Comercial"
              placeholder="Digite o telefone comercial"
              required
              :rules="phoneRules"
            />

            <Input
              id="update-company-address"
              name="address"
              v-model="form.address"
              type="text"
              label="Endereço"
              placeholder="Digite o endereço completo"
              required
              :rules="addressRules"
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
        </BaseForm>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseForm from '@/Components/Form/BaseForm.vue'
import Input from '@/Components/Input.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  company: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'company-data-updated'])

const { success, error } = useToast()
const page = usePage()

const loading = ref(false)

// Regras de validação para os campos
const nameRules = 'required'
const emailRules = 'email'
const phoneRules = 'required'
const addressRules = 'required'

const form = ref({
  name: '',
  cnpj: '',
  email: '',
  phone: '',
  address: ''
})

// Preencher formulário quando o modal abrir
watch(() => props.show, (newValue) => {
  if (newValue && props.company) {
    form.value = {
      name: props.company.name || '',
      cnpj: props.company.cnpj || '',
      email: props.company.email || '',
      phone: props.company.phone || '',
      address: props.company.address || ''
    }
  }
})

const close = () => {
  emit('close')
}

const handleSubmit = async (values, { setErrors }) => {
  loading.value = true
  
  try {
    const response = await fetch('/api/company/data', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(values)
    })

    const data = await response.json()

    if (data.success) {
      success('Dados da empresa atualizados com sucesso!')
      emit('company-data-updated', data.company)
      close()
    } else {
      if (data.errors) {
        setErrors(data.errors)
      } else {
        error(data.message || 'Erro ao atualizar dados da empresa')
      }
    }
  } catch (err) {
    error('Erro ao atualizar dados da empresa')
    console.error('Erro:', err)
  } finally {
    loading.value = false
  }
}
</script>
