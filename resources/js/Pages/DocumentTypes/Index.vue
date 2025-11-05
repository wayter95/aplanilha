<template>
  <AppLayout :title="'Tipos de Documentos'" :description="'Gerencie os tipos de documentos disponíveis'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="p-6 bg-default-50 dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-default-900">Tipos de Documentos</h2>
            <div class="flex gap-2">
              <button @click="openCreateTab" class="ti-btn btn-wave ti-btn-primary-full !py-2 !px-3">
                <i class="ri-add-line mr-1"></i>Novo Tipo
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-auto bg-transparent dark:bg-bgdark">
              <thead class="bg-transparent dark:bg-transparent">
                <tr class="text-left text-default-600 dark:text-default-300 border-b border-default-200 dark:border-white/10">
                  <th class="bg-transparent dark:bg-transparent">Nome</th>
                  <th class="bg-transparent dark:bg-transparent">Código</th>
                  <th class="bg-transparent dark:bg-transparent">Descrição</th>
                  <th class="bg-transparent dark:bg-transparent">Status</th>
                  <th class="bg-transparent dark:bg-transparent">Ordem</th>
                  <th class="text-right bg-transparent dark:bg-transparent">Ações</th>
                </tr>
              </thead>
              <tbody class="bg-transparent dark:bg-transparent">
                <tr v-for="type in types" :key="type.id" class="border-b border-default-200 dark:border-white/10 bg-transparent dark:bg-transparent">
                  <td class="bg-transparent dark:bg-transparent">{{ type.name }}</td>
                  <td class="bg-transparent dark:bg-transparent">
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ type.code }}</code>
                  </td>
                  <td class="bg-transparent dark:bg-transparent text-gray-600 dark:text-gray-400">{{ type.description || '-' }}</td>
                  <td class="bg-transparent dark:bg-transparent">
                    <span :class="type.is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">
                      {{ type.is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                  </td>
                  <td class="bg-transparent dark:bg-transparent">{{ type.sort_order }}</td>
                  <td class="text-right space-x-2 bg-transparent dark:bg-transparent">
                    <button class="ti-btn ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem]" @click="openEditTab(type)">Editar</button>
                    <button class="ti-btn ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem]" @click="showDeleteModal = true; selectedType = type">Remover</button>
                  </td>
                </tr>
                <tr v-if="types.length === 0">
                  <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 bg-transparent dark:bg-transparent">
                    Nenhum tipo cadastrado
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <DeleteDocumentTypeModal 
      :show="showDeleteModal" 
      :type="selectedType"
      @close="showDeleteModal = false"
      @type-deleted="handleTypeDeleted"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import DeleteDocumentTypeModal from '@/Components/DocumentTypesModals/DeleteDocumentTypeModal.vue'
import { useTabsStore } from '@/stores/useTabsStore'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, DeleteDocumentTypeModal },
  setup() {
    const tabsStore = useTabsStore()
    const toast = useToast()
    return { tabsStore, toast }
  },
  data() {
    return {
      user: this.$page.props.user || null,
      types: [],
      showDeleteModal: false,
      selectedType: null,
    }
  },
  created() {
    this.fetchTypes()
  },
  methods: {
    async fetchTypes() {
      try {
        const { data } = await window.axios.get('/api/document-types')
        this.types = data
      } catch (error) {
        console.error('Erro ao buscar tipos:', error)
        this.types = []
      }
    },
    openCreateTab() {
      const tempKey = `new-${Date.now()}`
      const path = `/document-types/new/${tempKey}`
      const ok = this.tabsStore.addTab({
        key: tempKey,
        title: 'Novo Tipo',
        mode: 'create',
        componentName: 'DocumentTypesForm',
        path,
        props: { mode: 'create', tempKey },
        context: 'document-types'
      })
      if (!ok) return this.toast.error('Limite de abas atingido')
      this.$inertia.visit(path)
    },
    openEditTab(type) {
      const exists = this.tabsStore.tabs.find(t => t.key === type.id)
      if (exists) {
        this.tabsStore.setActive(exists)
        return this.$inertia.visit(exists.path || `/document-types/${type.id}/edit`)
      }
      const ok = this.tabsStore.addTab({
        key: type.id,
        title: type.name,
        mode: 'edit',
        componentName: 'DocumentTypesForm',
        path: `/document-types/${type.id}/edit`,
        props: { mode: 'edit', id: type.id },
        context: 'document-types'
      })
      if (!ok) return this.toast.error('Limite de abas atingido')
      this.$inertia.visit(`/document-types/${type.id}/edit`)
    },
    handleTypeDeleted() {
      this.showDeleteModal = false
      this.selectedType = null
      this.fetchTypes()
    },
  }
}
</script>

