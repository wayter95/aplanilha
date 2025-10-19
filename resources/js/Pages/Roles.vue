<template>
  <AppLayout :title="'Funções'" :description="'Gerenciar funções do sistema'" :user="user">
    <!-- Start::row-1 -->
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <!-- DataTable Component -->
        <DataTable
          title="Lista de Funções"
          :data="roles.data"
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
          search-placeholder="Buscar funções..."
          @action="handleAction"
          @selection-change="handleSelectionChange"
          @filter-change="handleFilterChange"
          @search-change="handleSearchChange"
        >
          <!-- Custom Header Actions -->
          <template #header-actions>
            <button 
              @click="showCreateModal = true"
              class="ti-btn btn-wave ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]"
            >
              <i class="ri-add-line font-semibold align-middle"></i>
              Nova Função
            </button>
          </template>
        </DataTable>
      </div>
    </div>
    <!-- End::row-1 -->

    <!-- Modals -->
    <CreateRoleModal 
      :show="showCreateModal" 
      :available-permissions="availablePermissions"
      @close="showCreateModal = false"
      @role-created="handleRoleCreated"
    />
    
    <UpdateRoleModal 
      :show="showUpdateModal" 
      :role="selectedRole"
      :available-permissions="availablePermissions"
      @close="showUpdateModal = false"
      @role-updated="handleRoleUpdated"
    />
    
    <DeleteRoleModal 
      :show="showDeleteModal" 
      :role="selectedRole"
      @close="showDeleteModal = false"
      @role-deleted="handleRoleDeleted"
    />
  </AppLayout>
</template>

<script setup>
import DataTable from '@/Components/DataTable.vue'
import CreateRoleModal from '@/Components/RolesModals/CreateRoleModal.vue'
import DeleteRoleModal from '@/Components/RolesModals/DeleteRoleModal.vue'
import UpdateRoleModal from '@/Components/RolesModals/UpdateRoleModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

// Props from Inertia
const props = defineProps({
  roles: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  availablePermissions: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Modal states
const showCreateModal = ref(false)
const showUpdateModal = ref(false)
const showDeleteModal = ref(false)
const selectedRole = ref(null)

// Table configuration
const columns = [
  {
    key: 'name',
    label: 'Nome',
    type: 'text',
    sortable: true
  },
  {
    key: 'display_name',
    label: 'Nome de Exibição',
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
    key: 'permissions',
    label: 'Permissões',
    type: 'badge',
    sortable: false,
    badgeClass: (permissions) => {
      const count = permissions?.length || 0;
      return count > 0 ? 'bg-info/10 text-info' : 'bg-gray/10 text-gray';
    }
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

// Dynamic filters based on backend data
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

// Store current filters and search (initialize with backend values)
const currentFilters = ref({ ...props.filters })
const currentSearch = ref(props.filters.search || '')

// Event handlers
const handleAction = ({ action, row }) => {
  selectedRole.value = row
  
  switch (action) {
    case 'edit':
      showUpdateModal.value = true
      break
    case 'delete':
      showDeleteModal.value = true
      break
  }
}

const handleSelectionChange = (selectedItems) => {
  console.log('Selected items:', selectedItems)
  // Handle bulk actions here
}

const handleFilterChange = (filters) => {
  console.log('Filter changed:', filters)
  console.log('Current filters before update:', currentFilters.value)
  // Update current filters with new values
  currentFilters.value = { ...filters }
  console.log('Current filters after update:', currentFilters.value)
  reloadWithFilters()
}

const handleSearchChange = (searchQuery) => {
  console.log('Search changed:', searchQuery)
  currentSearch.value = searchQuery
  reloadWithFilters()
}

const reloadWithFilters = () => {
  const queryParams = {
    ...currentFilters.value,
    search: currentSearch.value
  }
  
  console.log('Query params before cleanup:', queryParams)
  
  // Remove empty values
  Object.keys(queryParams).forEach(key => {
    if (!queryParams[key] || queryParams[key] === '') {
      delete queryParams[key]
    }
  })
  
  console.log('Final query params:', queryParams)
  
  router.visit('/roles', {
    method: 'get',
    data: queryParams,
    preserveState: false,
    preserveScroll: false
  })
}

const handleRoleCreated = () => {
  showCreateModal.value = false
  // Reload page to show updated data
  router.visit('/roles', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}

const handleRoleUpdated = () => {
  showUpdateModal.value = false
  selectedRole.value = null
  // Reload page to show updated data
  router.visit('/roles', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}

const handleRoleDeleted = () => {
  showDeleteModal.value = false
  selectedRole.value = null
  // Reload page to show updated data
  router.visit('/roles', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}
</script>
