<template>
  <AppLayout title="" description="">
    <!-- Breadcrumb -->
    <Breadcrumb
      title="Projetos"
      :items="breadcrumbItems"
    />
    
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <DataTable
          title="Lista de Projetos"
          :data="projects.data"
          :columns="columns"
          :actions="[{}]"
          :filters="filterOptions"
          :server-side-filtering="true"
          :initial-filters="props.filters"
          :initial-search="props.filters.search || ''"
          :show-select-all="false"
          :show-search="true"
          :show-export="true"
          :show-filters="true"
          actions-align="left"
          search-placeholder="Buscar projetos..."
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
              Novo Projeto
            </Button>
          </template>
          
          <template #cell-project_type="{ row }">
            <div v-if="row.project_type" class="flex items-center gap-2">
              <div 
                class="w-3 h-3 rounded-full border border-gray-300 dark:border-gray-600" 
                :style="{ backgroundColor: row.project_type.color }"
              ></div>
              <span class="text-sm">{{ row.project_type.title }}</span>
            </div>
            <span v-else class="text-gray-400 text-sm">-</span>
          </template>

          <template #cell-status="{ row }">
            <StatusBadge :status="row.status" />
          </template>

          <template #cell-responsible_user="{ row }">
            <span v-if="row.responsible_user" class="text-sm">
              {{ row.responsible_user.name }}
            </span>
            <span v-else class="text-gray-400 text-sm">-</span>
          </template>

          <template #cell-manager_user="{ row }">
            <span v-if="row.manager_user" class="text-sm">
              {{ row.manager_user.name }}
            </span>
            <span v-else class="text-gray-400 text-sm">-</span>
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
    
    <Modal 
      :show="showDeleteModal" 
      @close="showDeleteModal = false"
      title="Excluir Projeto"
      max-width="md"
    >
      <div class="p-6">
        <p class="text-gray-700 dark:text-gray-300 mb-4">
          Tem certeza que deseja excluir o projeto <strong>{{ selectedProject?.name }}</strong>?
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Esta ação não pode ser desfeita.
        </p>
      </div>
      
      <template #footer>
        <div class="flex justify-end gap-3">
          <Button
            variant="secondary"
            @click="showDeleteModal = false"
          >
            Cancelar
          </Button>
          <Button
            variant="danger"
            @click="confirmDelete"
          >
            Excluir
          </Button>
        </div>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import DataTable from '@/Components/DataTable.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import StatusBadge from '@/Components/Common/StatusBadge.vue'
import ActionButtons from '@/Components/Common/ActionButtons.vue'
import Button from '@/Components/Button.vue'
import Modal from '@/Components/Modal.vue'
import { useToast } from '@/composables/useToast'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import projectsService from '@/api/projectsService'

const props = defineProps({
  projects: {
    type: Object,
    required: true
  },
  projectTypes: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const toast = useToast()
const showDeleteModal = ref(false)
const selectedProject = ref(null)

const columns = [
  {
    key: 'project_number',
    label: 'Número',
    type: 'text',
    sortable: true,
    width: '10%',
    align: 'left'
  },
  {
    key: 'name',
    label: 'Nome',
    type: 'text',
    sortable: true,
    width: '25%',
    align: 'left'
  },
  {
    key: 'project_type',
    label: 'Tipo',
    type: 'text',
    sortable: false,
    width: '15%',
    align: 'left'
  },
  {
    key: 'status',
    label: 'Status',
    type: 'text',
    sortable: true,
    width: '10%',
    align: 'center'
  },
  {
    key: 'responsible_user',
    label: 'Responsável',
    type: 'text',
    sortable: false,
    width: '15%',
    align: 'left'
  },
  {
    key: 'manager_user',
    label: 'Gerente',
    type: 'text',
    sortable: false,
    width: '15%',
    align: 'left'
  },
  {
    key: 'created_at',
    label: 'Data de Criação',
    type: 'date',
    sortable: true,
    width: '10%',
    align: 'left'
  }
]

const breadcrumbItems = [
  { label: 'Início', href: '/' },
  { label: 'Projetos' }
]

const filterOptions = computed(() => {
  const options = [
    {
      key: 'status',
      label: 'Status',
      options: [
        { value: 'active', label: 'Ativo' },
        { value: 'pending', label: 'Pendente' },
        { value: 'cancelled', label: 'Cancelado' },
        { value: 'completed', label: 'Completo' }
      ]
    }
  ]

  if (props.projectTypes && props.projectTypes.length > 0) {
    options.push({
      key: 'project_type',
      label: 'Tipo de Projeto',
      options: props.projectTypes.map(type => ({
        value: type.id,
        label: type.title
      }))
    })
  }

  return options
})

const currentFilters = ref({ ...props.filters })
const currentSearch = ref(props.filters.search || '')

const handleDeleteClick = (row) => {
  selectedProject.value = row
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    const data = await projectsService.delete(selectedProject.value.id)
    
    if (data.success) {
      toast.success(data.message)
      showDeleteModal.value = false
      selectedProject.value = null
      router.reload({ only: ['projects'] })
    } else {
      toast.error(data.message)
    }
  } catch (error) {
    toast.error('Erro ao excluir projeto')
  }
}

const createNew = () => {
  router.visit('/projects/create')
}

const editRecord = (id) => {
  router.visit(`/projects/${id}/edit`)
}

const handleFilterChange = (filters) => {
  currentFilters.value = filters
  router.get('/projects', {
    ...filters,
    search: currentSearch.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleSearchChange = (search) => {
  currentSearch.value = search
  router.get('/projects', {
    ...currentFilters.value,
    search: search
  }, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
  