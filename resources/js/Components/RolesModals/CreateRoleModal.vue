<template>
  <Modal :show="show" @close="closeModal" max-width="md">
    <div class="box-header">
      <h6 class="box-title">Criar Nova Função</h6>
      <button @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <i class="ri-close-line text-lg"></i>
      </button>
    </div>
    <div class="box-body">
      <form @submit.prevent="createRole">
        <div class="mb-4">
          <label for="name" class="form-label">Nome</label>
          <input type="text" class="ti-form-input" id="name" v-model="form.name" required>
          <p v-if="form.errors.name" class="text-danger text-xs mt-1">{{ form.errors.name }}</p>
        </div>
        
        <div class="mb-4">
          <label for="display_name" class="form-label">Nome de Exibição</label>
          <input type="text" class="ti-form-input" id="display_name" v-model="form.display_name" required>
          <p v-if="form.errors.display_name" class="text-danger text-xs mt-1">{{ form.errors.display_name }}</p>
        </div>
        
        <div class="mb-4">
          <label for="description" class="form-label">Descrição</label>
          <textarea class="ti-form-input" id="description" v-model="form.description" rows="3"></textarea>
          <p v-if="form.errors.description" class="text-danger text-xs mt-1">{{ form.errors.description }}</p>
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
                :id="`permission-${permission.id}`"
                :value="permission.id"
                v-model="form.permissions"
                class="form-check-input me-2"
              >
              <label :for="`permission-${permission.id}`" class="text-sm text-gray-700 dark:text-gray-300">
                {{ `${permission.module} - ${permission.action}` }}
              </label>
            </div>
            <p v-if="availablePermissions.length === 0" class="text-gray-500 text-sm">
              Nenhuma permissão disponível
            </p>
          </div>
        </div>
        
        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="ti-btn ti-btn-light">Cancelar</button>
          <button type="submit" class="ti-btn ti-btn-primary" :disabled="form.processing">
            <span v-if="form.processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Criar
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()

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
  permissions: [],
  errors: {}
})

const createRole = async () => {
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
      emit('role-created', result.role)
      closeModal()
    } else {
      console.error('Error creating role:', result.message)
      alert('Erro ao criar função: ' + result.message)
    }
  } catch (error) {
    console.error('Error creating role:', error)
    alert('Erro ao criar função')
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
