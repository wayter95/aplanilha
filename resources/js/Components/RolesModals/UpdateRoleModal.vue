<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Editar Função
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="updateRole">
          <div class="space-y-4">
        <Input
          id="update-role-name"
          v-model="form.name"
          type="text"
          label="Nome (identificador único)"
          placeholder="Digite o nome da função"
          required
          :error="form.errors?.name"
        />
        
        <Input
          id="update-role-display-name"
          v-model="form.display_name"
          type="text"
          label="Nome de Exibição"
          placeholder="Digite o nome de exibição"
          required
          :error="form.errors?.display_name"
        />
        
        <div class="mb-4">
          <label for="edit-description" class="form-label">Descrição</label>
          <textarea class="ti-form-input" id="edit-description" v-model="form.description" rows="3" placeholder="Digite uma descrição para a função"></textarea>
          <p v-if="form.errors?.description" class="text-danger text-xs mt-1">{{ form.errors.description }}</p>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Status
          </label>
          <select v-model="form.status" required class="ti-form-select">
            <option value="">Selecione um status</option>
            <option value="Ativo">Ativo</option>
            <option value="Inativo">Inativo</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Permissões
          </label>
          <div class="border rounded-lg p-3 max-h-48 overflow-y-auto">
            <div v-for="permission in availablePermissions" :key="permission.id" class="flex items-center mb-2">
              <input 
                type="checkbox" 
                :id="`edit-permission-${permission.id}`"
                :value="permission.id"
                v-model="form.permissions"
                class="form-check-input me-2"
              >
              <label :for="`edit-permission-${permission.id}`" class="text-sm text-gray-700 dark:text-gray-300">
                {{ `${permission.module} - ${permission.action}` }}
              </label>
            </div>
            <p v-if="availablePermissions.length === 0" class="text-gray-500 text-sm">
              Nenhuma permissão disponível
            </p>
          </div>
        </div>
        
            <div class="flex justify-end gap-2 mt-6">
              <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
              <button type="submit" class="ti-btn ti-btn-primary" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Atualizar
              </button>
            </div>
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

const page = usePage()
const { success, error } = useToast()

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  role: {
    type: Object,
    default: null
  },
  availablePermissions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'role-updated'])

const form = ref({
  name: '',
  display_name: '',
  description: '',
  status: '',
  permissions: [],
  errors: {}
})

watch(() => props.role, (newRole) => {
  if (newRole) {
    form.value = {
      name: newRole.name || '',
      display_name: newRole.display_name || '',
      description: newRole.description || '',
      status: newRole.is_active ? 'Ativo' : 'Inativo',
      permissions: newRole.permissions ? newRole.permissions.map(p => p.id) : [],
      errors: {}
    }
  }
}, { immediate: true })

const updateRole = async () => {
  if (!props.role || !props.role.id) {
    console.error('No role ID provided')
    return
  }

  try {
    const dataToSend = {
      ...form.value,
      is_active: form.value.status === 'Ativo'
    }

    const response = await fetch(`/api/roles/${props.role.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify(dataToSend)
    })

    const result = await response.json()

    if (result.success) {
      success('Função atualizada com sucesso!')
      emit('role-updated', result.role)
      closeModal()
    } else {
      console.error('Error updating role:', result.message)
      error('Erro ao atualizar função: ' + result.message)
    }
  } catch (error) {
    console.error('Error updating role:', error)
    error('Erro ao atualizar função')
  }
}

const closeModal = () => {
  form.value = {
    name: '',
    display_name: '',
    description: '',
    status: '',
    permissions: [],
    errors: {}
  }
  emit('close')
}
</script>
