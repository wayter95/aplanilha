<template>
  <AppLayout title="Usuários" description="Gerenciar usuários do sistema">
    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
      <div>
        <h3 class="!text-defaulttextcolor dark:!text-white text-[1.125rem] font-semibold">
          Usuários
        </h3>
      </div>
      <ol class="flex items-center whitespace-nowrap min-w-0">
        <li class="text-[0.813rem] ps-[0.5rem]">
          <a class="flex items-center text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
            Sistema
            <i class="ti ti-chevrons-right flex-shrink-0 text-textmuted dark:text-textmuted px-[0.5rem] overflow-visible rtl:rotate-180"></i>
          </a>
        </li>
        <li class="text-[0.813rem] text-defaulttextcolor font-semibold hover:text-primary dark:text-white" aria-current="page">
          Usuários
        </li>
      </ol>
    </div>
    <!-- Page Header Close -->

    <!-- Start::row-1 -->
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <!-- DataTable Component -->
        <DataTable
          title="Usuários"
          :data="users.data"
          :columns="columns"
          :actions="actions"
          :filters="filters"
          :show-select-all="true"
          :show-search="true"
          :show-export="true"
          :show-filters="true"
          search-placeholder="Buscar usuários..."
          @action="handleAction"
          @selection-change="handleSelectionChange"
        >
          <!-- Custom Header Actions -->
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
    <!-- End::row-1 -->

    <!-- Modals -->
    <CreateUserModal 
      :show="showCreateModal" 
      @close="showCreateModal = false"
      @user-created="handleUserCreated"
    />
    
    <UpdateUserModal 
      :show="showUpdateModal" 
      :user="selectedUser"
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
import { ref } from 'vue'

// Props from Inertia
const props = defineProps({
  users: {
    type: Object,
    required: true
  }
})

// Modal states
const showCreateModal = ref(false)
const showUpdateModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

// Table configuration
const columns = [
  {
    key: 'name',
    label: 'Nome do Usuário',
    type: 'user',
    nameKey: 'name',
    emailKey: 'email',
    sortable: true
  },
  {
    key: 'status',
    label: 'Status',
    type: 'status',
    sortable: true
  },
  {
    key: 'role',
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

const filters = [
  {
    key: 'status',
    label: 'Status',
    options: [
      { value: 'Ativo', label: 'Ativo' },
      { value: 'Inativo', label: 'Inativo' },
      { value: 'Pendente', label: 'Pendente' },
      { value: 'Bloqueado', label: 'Bloqueado' }
    ]
  },
  {
    key: 'role',
    label: 'Função',
    options: [
      { value: 'admin', label: 'Administrador' },
      { value: 'user', label: 'Usuário' },
      { value: 'moderator', label: 'Moderador' },
      { value: 'guest', label: 'Convidado' }
    ]
  }
]

// Event handlers
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
  console.log('Selected items:', selectedItems)
  // Handle bulk actions here
}

const handleUserCreated = () => {
  showCreateModal.value = false
  // Reload page to show updated data
  window.location.reload()
}

const handleUserUpdated = () => {
  showUpdateModal.value = false
  selectedUser.value = null
  // Reload page to show updated data
  window.location.reload()
}

const handleUserDeleted = () => {
  showDeleteModal.value = false
  selectedUser.value = null
  // Reload page to show updated data
  window.location.reload()
}
</script>