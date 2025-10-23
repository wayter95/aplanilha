<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

      <!-- Modal -->
      <div
        class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"
      >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Criar Nova Função
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- ✅ Formulário com validação -->
        <BaseForm @submit="createRole">
          <div class="space-y-4">
            <!-- Nome -->
            <div>
              <Input
                id="create-role-name"
                name="name"
                v-model="form.name"
                type="text"
                label="Nome (identificador único)"
                placeholder="Digite o nome da função"
                required
                :rules="nameRules"
              />
              <p v-if="errors.name" class="text-danger text-xs mt-1">{{ errors.name }}</p>
            </div>

            <!-- Nome de Exibição -->
            <div>
              <Input
                id="create-role-display-name"
                name="display_name"
                v-model="form.display_name"
                type="text"
                label="Nome de Exibição"
                placeholder="Digite o nome de exibição"
                required
                :rules="displayNameRules"
              />
              <p v-if="errors.display_name" class="text-danger text-xs mt-1">{{ errors.display_name }}</p>
            </div>

            <!-- Descrição -->
            <div>
              <label for="description" class="form-label">Descrição</label>
              <textarea
                id="description"
                name="description"
                v-model="form.description"
                class="ti-form-input"
                placeholder="Digite uma descrição para a função"
                :class="{'border-danger': errors.description}"
                v-validate
                :rules="descriptionRules"
              ></textarea>
              <p v-if="errors.description" class="text-danger text-xs mt-1">{{ errors.description }}</p>
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

            <!-- Permissões -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Permissões
              </label>
              <div class="border rounded-lg p-3 max-h-48 overflow-y-auto">
                <div
                  v-for="permission in availablePermissions"
                  :key="permission.id"
                  class="flex items-center mb-2"
                >
                  <input
                    type="checkbox"
                    :id="`permission-${permission.id}`"
                    :value="permission.id"
                    v-model="form.permissions"
                    name="permissions"
                    v-validate
                    :rules="permissionsRules"
                    class="form-check-input me-2"
                  >
                  <label
                    :for="`permission-${permission.id}`"
                    class="text-sm text-gray-700 dark:text-gray-300"
                  >
                    {{ `${permission.module} - ${permission.action}` }}
                  </label>
                </div>
                <p v-if="availablePermissions.length === 0" class="text-gray-500 text-sm">
                  Nenhuma permissão disponível
                </p>
              </div>
              <p v-if="errors.permissions" class="text-danger text-xs mt-1">{{ errors.permissions }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
            <button type="submit" class="ti-btn ti-btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Criar
            </button>
          </div>
        </BaseForm>
      </div>
    </div>
  </div>
</template>

<script setup>
import Input from '@/Components/Input.vue'
import BaseForm from '@/Components/Form/BaseForm.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useForm } from 'vee-validate'

const page = usePage()
const { success, error } = useToast()

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  availablePermissions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'role-created'])

const form = ref({
  name: '',
  display_name: '',
  description: '',
  status: '',
  permissions: []
})

// 🧭 Regras de validação
const nameRules = 'required|min:2|max:50'
const displayNameRules = 'required|min:2|max:100'
const descriptionRules = 'max:255'
const statusRules = 'required'
const permissionsRules = 'required'

// 📌 vee-validate
const { errors, handleSubmit, isSubmitting } = useForm()

const createRole = handleSubmit(async () => {
  try {
    const dataToSend = {
      ...form.value,
      is_active: form.value.status === 'Ativo'
    }

    const response = await fetch('/api/roles', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(dataToSend)
    })

    const result = await response.json()

    if (result.success) {
      success('Função criada com sucesso!')
      emit('role-created', result.role)
      closeModal()
    } else {
      console.error('Error creating role:', result.message)
      error('Erro ao criar função: ' + result.message)
    }
  } catch (err) {
    console.error('Error creating role:', err)
    error('Erro ao criar função')
  }
})

const closeModal = () => {
  emit('close')
}
</script>
