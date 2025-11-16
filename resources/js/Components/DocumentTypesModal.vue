<template>
  <Modal :show="show" @close="$emit('close')" title="Gerenciar Tipos de Documentos" size="lg">
    <div class="space-y-4">
      <div class="flex justify-end mb-4">
        <button
          @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors"
        >
          <i class="ri-add-line mr-1"></i>Novo Tipo
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="text-left py-2 px-3 text-gray-700 dark:text-gray-300">Nome</th>
              <th class="text-left py-2 px-3 text-gray-700 dark:text-gray-300">Código</th>
              <th class="text-left py-2 px-3 text-gray-700 dark:text-gray-300">Descrição</th>
              <th class="text-left py-2 px-3 text-gray-700 dark:text-gray-300">Status</th>
              <th class="text-left py-2 px-3 text-gray-700 dark:text-gray-300">Ordem</th>
              <th class="text-right py-2 px-3 text-gray-700 dark:text-gray-300">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="type in types"
              :key="type.id"
              class="border-b border-gray-200 dark:border-gray-700"
            >
              <td class="py-2 px-3">{{ type.name }}</td>
              <td class="py-2 px-3">
                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ type.code }}</code>
              </td>
              <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ type.description || '-' }}</td>
              <td class="py-2 px-3">
                <span
                  :class="type.is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-500'"
                >
                  {{ type.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="py-2 px-3">{{ type.sort_order }}</td>
              <td class="py-2 px-3 text-right space-x-2">
                <button
                  @click="openEditModal(type)"
                  class="px-2 py-1 text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  Editar
                </button>
                <button
                  @click="confirmDelete(type)"
                  class="px-2 py-1 text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                >
                  Excluir
                </button>
              </td>
            </tr>
            <tr v-if="types.length === 0">
              <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                Nenhum tipo cadastrado
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <template #footer>
      <button
        @click="$emit('close')"
        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
      >
        Fechar
      </button>
    </template>
  </Modal>

  <Modal
    v-if="showFormModal"
    :show="showFormModal"
    @close="closeFormModal"
    :title="editingType ? 'Editar Tipo' : 'Novo Tipo'"
    size="md"
  >
    <Form @submit="saveType" :initial-values="formData">
      <div class="space-y-4">
        <Input
          name="name"
          label="Nome"
          rules="required"
          v-model="formData.name"
          placeholder="Ex: Contratos"
        />
        <Input
          name="code"
          label="Código"
          rules="required"
          v-model="formData.code"
          placeholder="Ex: contract"
          :disabled="!!editingType"
        />
        <Textarea
          name="description"
          label="Descrição"
          v-model="formData.description"
          :rows="3"
          placeholder="Descrição do tipo de documento"
        />
        <div class="flex gap-4">
          <div class="flex-1">
            <Input
              name="sort_order"
              label="Ordem"
              type="number"
              v-model="formData.sort_order"
            />
          </div>
          <div class="flex-1 flex items-center pt-6">
            <Switch
              name="is_active"
              label="Ativo"
              v-model="formData.is_active"
            />
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="closeFormModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors"
          >
            Salvar
          </button>
        </div>
      </template>
    </Form>
  </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import Modal from '@/Components/Modal.vue'
import Input from '@/Components/Input.vue'
import Textarea from '@/Components/Textarea.vue'
import Switch from '@/Components/Switch.vue'
import { Form as VeeForm } from 'vee-validate'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'updated'])

const types = ref([])
const showFormModal = ref(false)
const editingType = ref(null)
const formData = ref({
  name: '',
  code: '',
  description: '',
  is_active: true,
  sort_order: 0
})

watch(() => props.show, async (newVal) => {
  if (newVal) {
    await fetchTypes()
  }
})

async function fetchTypes() {
  try {
    const { data } = await window.axios.get('/document-types')
    types.value = data
  } catch (error) {
    console.error('Erro ao buscar tipos:', error)
    types.value = []
  }
}

function openCreateModal() {
  editingType.value = null
  formData.value = {
    name: '',
    code: '',
    description: '',
    is_active: true,
    sort_order: 0
  }
  showFormModal.value = true
}

function openEditModal(type) {
  editingType.value = type
  formData.value = {
    name: type.name,
    code: type.code,
    description: type.description || '',
    is_active: type.is_active,
    sort_order: type.sort_order
  }
  showFormModal.value = true
}

function closeFormModal() {
  showFormModal.value = false
  editingType.value = null
}

async function saveType(values) {
  try {
    const data = values || formData.value
    if (editingType.value) {
      await window.axios.put(`/document-types/${editingType.value.id}`, data)
    } else {
      await window.axios.post('/document-types', data)
    }
    await fetchTypes()
    closeFormModal()
    emit('updated')
  } catch (error) {
    const message = error?.response?.data?.message || 'Erro ao salvar tipo'
    window?.alert?.(message)
  }
}

async function confirmDelete(type) {
  if (!confirm(`Tem certeza que deseja excluir o tipo "${type.name}"?`)) {
    return
  }

  try {
    await window.axios.delete(`/document-types/${type.id}`)
    await fetchTypes()
    emit('updated')
  } catch (error) {
    const message = error?.response?.data?.message || 'Erro ao excluir tipo'
    window?.alert?.(message)
  }
}
</script>

