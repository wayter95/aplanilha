<template>
  <AppLayout :title="'Tipos de Documentos'" :description="'Gerencie os tipos de documentos disponíveis'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <DataTable
          title="Lista de Tipos de Documentos"
          :data="types.data"
          :columns="columns"
          :actions="actions"
          :filters="filterOptions"
          :server-side-filtering="true"
          :initial-filters="props.filters"
          :initial-search="props.filters.search || ''"
          :show-select-all="true"
          :show-search="true"
          :show-export="true"
          :show-filters="true"
          search-placeholder="Buscar tipos de documentos..."
          @action="handleAction"
          @selection-change="handleSelectionChange"
          @filter-change="handleFilterChange"
          @search-change="handleSearchChange"
        >
          <template #header-actions>
            <button 
              @click="openCreateTab"
              class="ti-btn btn-wave ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]"
            >
              <i class="ri-add-line font-semibold align-middle"></i>
              Novo Tipo
            </button>
          </template>
          
          <template #cell-code="{ value }">
            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ value }}</code>
          </template>
        </DataTable>
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

<script setup>
import DataTable from '@/Components/DataTable.vue'
import DeleteDocumentTypeModal from '@/Components/DocumentTypesModals/DeleteDocumentTypeModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'
import { useToast } from '@/composables/useToast'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  types: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const tabsStore = useTabsMemoryStore()
const toast = useToast()
const showDeleteModal = ref(false)
const selectedType = ref(null)

const columns = [
  {
    key: 'name',
    label: 'Nome',
    type: 'text',
    sortable: true
  },
  {
    key: 'code',
    label: 'Código',
    type: 'text',
    sortable: true
  },
  {
    key: 'description',
    label: 'Descrição',
    type: 'text',
    sortable: true
  },
  {
    key: 'is_active',
    label: 'Status',
    type: 'status',
    sortable: true
  },
  {
    key: 'sort_order',
    label: 'Ordem',
    type: 'text',
    sortable: true
  },
  {
    key: 'created_at',
    label: 'Data de Criação',
    type: 'date',
    sortable: true
  }
]

const actions = [
  {
    name: 'edit',
    label: 'Editar',
    icon: 'ri-edit-line',
    class: 'ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem] !m-0'
  },
  {
    name: 'delete',
    label: 'Excluir',
    icon: 'ri-delete-bin-line',
    class: 'ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem] !m-0'
  }
]

const filterOptions = computed(() => [
  {
    key: 'status',
    label: 'Status',
    options: [
      { value: 'Ativo', label: 'Ativo' },
      { value: 'Inativo', label: 'Inativo' }
    ]
  }
])

const currentFilters = ref({ ...props.filters })
const currentSearch = ref(props.filters.search || '')

const handleAction = ({ action, row }) => {
  selectedType.value = row
  
  switch (action) {
    case 'edit':
      openEditTab(row)
      break
    case 'delete':
      showDeleteModal.value = true
      break
  }
}

const handleSelectionChange = (selectedItems) => {
  // Handle bulk actions here
}

const handleFilterChange = (filters) => {
  currentFilters.value = { ...filters }
  reloadWithFilters()
}

const handleSearchChange = (searchQuery) => {
  currentSearch.value = searchQuery
  reloadWithFilters()
}

const reloadWithFilters = () => {
  const queryParams = {
    ...currentFilters.value,
    search: currentSearch.value
  }
  
  Object.keys(queryParams).forEach(key => {
    if (!queryParams[key] || queryParams[key] === '') {
      delete queryParams[key]
    }
  })
  
  router.visit('/document-types', {
    method: 'get',
    data: queryParams,
    preserveState: false,
    preserveScroll: false
  })
}

const openCreateTab = () => {
  const tempKey = `new-${Date.now()}`
  const path = `/document-types/new/${tempKey}`
  const ok = tabsStore.addTab({
    key: tempKey,
    title: 'Novo Tipo',
    mode: 'create',
    componentName: 'DocumentTypesForm',
    path,
    props: { mode: 'create', tempKey },
    context: 'document-types'
  })
  if (!ok) return toast.error('Limite de abas atingido')
  router.visit(path)
}

const openEditTab = (type) => {
  const exists = tabsStore.tabs.find(t => t.key === type.id)
  if (exists) {
    tabsStore.setActive(exists)
    return router.visit(exists.path || `/document-types/${type.id}/edit`)
  }
  const ok = tabsStore.addTab({
    key: type.id,
    title: type.name,
    mode: 'edit',
    componentName: 'DocumentTypesForm',
    path: `/document-types/${type.id}/edit`,
    props: { mode: 'edit', id: type.id },
    context: 'document-types'
  })
  if (!ok) return toast.error('Limite de abas atingido')
  router.visit(`/document-types/${type.id}/edit`)
}

const handleTypeDeleted = () => {
  showDeleteModal.value = false
  selectedType.value = null
  router.visit('/document-types', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}
</script>
