<template>
  <AppLayout title="Tipos de Projetos" description="Gerencie os tipos de projetos disponíveis">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <DataTable
          title="Lista de Tipos de Projetos"
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
          search-placeholder="Buscar tipos de projetos..."
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
          
          <template #cell-color="{ value }">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" :style="{ backgroundColor: value }"></div>
              <span class="text-xs">{{ value }}</span>
            </div>
          </template>

          <template #cell-status="{ value }">
            <span :class="['badge', value === 'a' ? 'bg-primary' : 'bg-secondary']">
              {{ value === 'a' ? 'Ativo' : 'Bloqueado' }}
            </span>
          </template>
        </DataTable>
      </div>
    </div>
    
    <DeleteProjectTypeModal 
      :show="showDeleteModal" 
      :type="selectedType"
      @close="showDeleteModal = false"
      @type-deleted="handleTypeDeleted"
    />
  </AppLayout>
</template>

<script setup>
import DataTable from '@/Components/DataTable.vue'
import DeleteProjectTypeModal from '@/Components/ProjectTypesModals/DeleteProjectTypeModal.vue'
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
    key: 'title',
    label: 'Título',
    type: 'text',
    sortable: true
  },
  {
    key: 'color',
    label: 'Cor',
    type: 'text',
    sortable: false
  },
  {
    key: 'status',
    label: 'Status',
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
    name: 'toggle-status',
    label: 'Ativar/Bloquear',
    icon: 'ri-toggle-line',
    class: 'ti-btn-outline-warning !py-1 !px-2 !text-[0.75rem] !m-0'
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
      { value: 'Bloqueado', label: 'Bloqueado' }
    ]
  }
])

const currentFilters = ref({ ...props.filters })
const currentSearch = ref(props.filters.search || '')

const handleAction = ({ action, row }) => {
  selectedType.value = row
  
  switch (action) {
    case 'edit':
      openEditTab(row.id)
      break
    case 'toggle-status':
      toggleStatus(row)
      break
    case 'delete':
      showDeleteModal.value = true
      break
  }
}

const openCreateTab = () => {
  const tempId = `temp_${Date.now()}`
  tabsStore.addTab({
    key: tempId,
    title: 'Novo Tipo de Projeto',
    componentName: 'ProjectTypes/Form',
    path: `/projects/types/new/${tempId}`,
    mode: 'create',
    props: { tempKey: tempId }
  })
  router.visit(`/projects/types/new/${tempId}`)
}

const openEditTab = (id) => {
  tabsStore.addTab({
    key: id,
    title: 'Editar Tipo de Projeto',
    componentName: 'ProjectTypes/Form',
    path: `/projects/types/${id}/edit`,
    mode: 'edit',
    props: { id }
  })
  router.visit(`/projects/types/${id}/edit`)
}

const toggleStatus = async (type) => {
  try {
    const endpoint = type.status === 'a' 
      ? `/api/project-types/${type.id}/block` 
      : `/api/project-types/${type.id}/activate`
    
    const response = await fetch(endpoint, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })

    const data = await response.json()
    
    if (data.success) {
      toast.success(data.message)
      router.reload({ only: ['types'] })
    } else {
      toast.error(data.message)
    }
  } catch (error) {
    toast.error('Erro ao alterar status do tipo de projeto')
  }
}

const handleTypeDeleted = () => {
  showDeleteModal.value = false
  selectedType.value = null
  router.reload({ only: ['types'] })
}

const handleSelectionChange = (selectedRows) => {
  console.log('Selected rows:', selectedRows)
}

const handleFilterChange = (filters) => {
  currentFilters.value = filters
  router.get('/projects/types', {
    ...filters,
    search: currentSearch.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleSearchChange = (search) => {
  currentSearch.value = search
  router.get('/projects/types', {
    ...currentFilters.value,
    search: search
  }, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
