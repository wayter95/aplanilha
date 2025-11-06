<template>
  <AppLayout :title="'Usuários'" :description="'Gerenciar usuários do sistema'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <DataTable
          title="Lista de Usuários"
          :data="users.data"
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
          search-placeholder="Buscar usuários..."
          @action="handleAction"
          @selection-change="handleSelectionChange"
          @filter-change="handleFilterChange"
          @search-change="handleSearchChange"
        >
          <template #header-actions>
            <button 
              @click="showCreateModal = true"
              class="ti-btn btn-wave ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]"
            >
              <i class="ri-add-line font-semibold align-middle"></i>
              Novo Usuário
            </button>
          </template>
        </DataTable>
      </div>
    </div>
    <CreateUserModal 
      :show="showCreateModal" 
      :available-roles="availableRoles"
      @close="showCreateModal = false"
      @user-created="handleUserCreated"
    />
    
    <UpdateUserModal 
      :show="showUpdateModal" 
      :user="selectedUser"
      :available-roles="availableRoles"
      @close="showUpdateModal = false"
      @user-updated="handleUserUpdated"
    />
    
    <DeleteUserModal 
      :show="showDeleteModal" 
      :user="selectedUser"
      @close="showDeleteModal = false"
      @user-deleted="handleUserDeleted"
    />
  </AppLayout>
</template>

<script setup>
import DataTable from '@/Components/DataTable.vue'
import CreateUserModal from '@/Components/UsersModals/CreateUserModal.vue'
import DeleteUserModal from '@/Components/UsersModals/DeleteUserModal.vue'
import UpdateUserModal from '@/Components/UsersModals/UpdateUserModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  users: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  availableRoles: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const showCreateModal = ref(false)
const showUpdateModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

const columns = [
    {
      key: 'name',
      label: 'Nome do Usuário',
      type: 'user',
      nameKey: 'name',
      emailKey: 'email',
      photoKey: 'photo_key', // Mudança: usar photo_key em vez de avatar_path
      sortable: true
    },
  {
    key: 'is_active',
    label: 'Status',
    type: 'status',
    sortable: true
  },
  {
    key: 'roles[0].display_name',
    label: 'Função',
    type: 'role',
    sortable: true
  },
  {
    key: 'email',
    label: 'Email',
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
  },
  {
    key: 'role',
    label: 'Função',
    options: props.availableRoles.map(role => ({
      value: role.name,
      label: role.display_name
    }))
  }
])

const handleAction = ({ action, row }) => {
  selectedUser.value = row
  
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
  // Handle bulk actions here
}

const currentFilters = ref({ ...props.filters })
const currentSearch = ref(props.filters.search || '')

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
  
  router.visit('/users', {
    method: 'get',
    data: queryParams,
    preserveState: false,
    preserveScroll: false
  })
}

const handleUserCreated = () => {
  showCreateModal.value = false
  router.visit('/users', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}

const handleUserUpdated = () => {
  showUpdateModal.value = false
  selectedUser.value = null
  router.visit('/users', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}

const handleUserDeleted = () => {
  showDeleteModal.value = false
  selectedUser.value = null
  router.visit('/users', {
    method: 'get',
    preserveState: false,
    preserveScroll: false
  })
}
</script>