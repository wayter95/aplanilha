<template>
  <AppLayout title="" description="">
    <!-- Breadcrumb -->
    <Breadcrumb
      title="Tipos de Projetos"
      :items="breadcrumbItems"
    />
    
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <DataTable
          title="Lista de Tipos de Projetos"
          :data="types.data"
          :columns="columns"
          :actions="[{}]"
          :filters="filterOptions"
          :server-side-filtering="true"
          :initial-filters="props.filters"
          :initial-search="props.filters.search || ''"
          :show-select-all="true"
          :show-search="true"
          :show-export="true"
          :show-filters="true"
          actions-align="left"
          search-placeholder="Buscar tipos de projetos..."
          @selection-change="handleSelectionChange"
          @filter-change="handleFilterChange"
          @search-change="handleSearchChange"
        >
          <template #header-actions>
            <Button
              variant="primary"
              style-type="outline"
              size="sm"
              left-icon="ri-add-line"
              @click="createNew"
            >
              Novo Tipo
            </Button>
          </template>
          
          <template #cell-color="{ value }">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" :style="{ backgroundColor: value }"></div>
              <span class="text-xs">{{ value }}</span>
            </div>
          </template>

          <template #cell-status="{ row }">
            <Switch
              :name="`status-${row.id}`"
              :model-value="row.status === 'a'"
              variant="primary"
              size="xs"
              :show-inline-label="false"
              :show-help-text="false"
              @update:model-value="toggleStatus(row)"
            />
          </template>
          
          <template #cell-actions="{ row }">
            <ActionButtons
              :show-edit="true"
              :show-delete="true"
              :show-toggle-status="false"
              @edit="editRecord(row.id)"
              @delete="handleDeleteClick(row)"
            />
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
import Breadcrumb from '@/Components/Breadcrumb.vue'
import StatusBadge from '@/Components/Common/StatusBadge.vue'
import ActionButtons from '@/Components/Common/ActionButtons.vue'
import Switch from '@/Components/Switch.vue'
import Button from '@/Components/Button.vue'
import { useToast } from '@/composables/useToast'
import { router} from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import projectTypesService from '@/api/projectTypesService'

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

const breadcrumbItems = [
  { label: 'Início', href: '/' },
  { label: 'Tipos de Projetos' }
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

const handleDeleteClick = (row) => {
  selectedType.value = row
  showDeleteModal.value = true
}

const createNew = () => {
  const tempId = `temp_${Date.now()}`
  router.visit(`/project-types/new/${tempId}`)
}

const editRecord = (id) => {
  router.visit(`/project-types/${id}/edit`)
}

const toggleStatus = async (type) => {
  try {
    const data = await projectTypesService.toggleStatus(type.id, type.status)
    
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
  // Handle selection change
}

const handleFilterChange = (filters) => {
  currentFilters.value = filters
  router.get('/project-types', {
    ...filters,
    search: currentSearch.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleSearchChange = (search) => {
  currentSearch.value = search
  router.get('/project-types', {
    ...currentFilters.value,
    search: search
  }, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
