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
        <div class="box custom-box">
          <div class="box-header flex items-center justify-between flex-wrap gap-4">
            <div class="box-title">
              Usuários
              <span class="badge bg-light text-defaulttextcolor rounded-full ms-1 text-[0.75rem] align-middle">
                {{ users.data.length }}
              </span>
            </div>
            <div class="flex flex-wrap gap-2">
              <button 
                @click="showCreateModal = true"
                class="ti-btn btn-wave ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]"
              >
                <i class="ri-add-line font-semibold align-middle"></i>
                Novo Usuário
              </button>
              <button 
                @click="exportCSV"
                type="button" 
                class="ti-btn btn-wave ti-btn-success !py-1 !px-2 !text-[0.75rem] !m-0"
              >
                Exportar CSV
              </button>
            </div>
          </div>
          
          <!-- Search and Filters -->
          <div class="box-body !p-0 border-b border-defaultborder">
            <div class="p-4">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                  <div class="search-input-container">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Buscar usuários..."
                      class="ti-form-input pr-4"
                      @input="handleSearch"
                    />
                    <i class="ri-search-line search-icon text-textmuted dark:text-textmuted text-sm"></i>
                  </div>
                </div>
                
                <!-- Status Filter -->
                <div>
                  <select
                    v-model="statusFilter"
                    @change="handleFilter"
                    class="ti-form-select"
                  >
                    <option value="">Todos os Status</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                  </select>
                </div>
                
                <!-- Role Filter -->
                <div>
                  <select
                    v-model="roleFilter"
                    @change="handleFilter"
                    class="ti-form-select"
                  >
                    <option value="">Todas as Funções</option>
                    <option value="admin">Administrador</option>
                    <option value="user">Usuário</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="box-body !p-0">
            <div class="table-responsive">
              <table class="table whitespace-nowrap min-w-full">
                <thead>
                  <tr>
                    <th scope="col">
                      <input 
                        v-model="selectAll"
                        class="form-check-input" 
                        type="checkbox" 
                        id="selectAll"
                        @change="handleSelectAll"
                      >
                    </th>
                    <th scope="col" class="text-start cursor-pointer" @click="sortBy('name')">
                      <div class="flex items-center gap-2">
                        <span>Nome do Usuário</span>
                        <i class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                      </div>
                    </th>
                    <th scope="col" class="text-start cursor-pointer" @click="sortBy('status')">
                      <div class="flex items-center gap-2">
                        <span>Status</span>
                        <i class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                      </div>
                    </th>
                    <th scope="col" class="text-start cursor-pointer" @click="sortBy('role')">
                      <div class="flex items-center gap-2">
                        <span>Função</span>
                        <i class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                      </div>
                    </th>
                    <th scope="col" class="text-start cursor-pointer" @click="sortBy('email')">
                      <div class="flex items-center gap-2">
                        <span>Email</span>
                        <i class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                      </div>
                    </th>
                    <th scope="col" class="text-start cursor-pointer" @click="sortBy('created_at')">
                      <div class="flex items-center gap-2">
                        <span>Data de Criação</span>
                        <i class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                      </div>
                    </th>
                    <th scope="col" class="text-start">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="user in paginatedData" 
                    :key="user.id"
                    class="border border-x-0 border-defaultborder crm-contact"
                  >
                    <td>
                      <input 
                        v-model="selectedUsers"
                        :value="user.id"
                        class="form-check-input" 
                        type="checkbox" 
                        :id="`user-${user.id}`"
                      >
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="leading-none">
                          <span class="avatar avatar-rounded avatar-sm">
                            <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold">
                              {{ getInitials(user.name) }}
                            </div>
                          </span>
                        </div>
                        <div>
                          <a href="javascript:void(0);" @click="showUserDetails(user)">
                            <span class="block font-semibold">{{ user.name }}</span>
                          </a>
                          <span class="block text-textmuted dark:text-textmuted text-[0.6875rem]">
                            <i class="ri-account-circle-line me-1 text-[0.8125rem] align-middle"></i>
                            Criado em {{ formatDate(user.created_at) }}
                          </span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span 
                        :class="[
                          'badge',
                          user.status === 'Ativo' 
                            ? 'bg-success/10 text-success' 
                            : 'bg-danger/10 text-danger'
                        ]"
                      >
                        {{ user.status }}
                      </span>
                    </td>
                    <td>
                      <span 
                        :class="[
                          'badge',
                          user.is_master 
                            ? 'bg-warning/10 text-warning' 
                            : 'bg-primary/10 text-primary'
                        ]"
                      >
                        {{ user.is_master ? 'Administrador' : 'Usuário' }}
                      </span>
                    </td>
                    <td>
    <div>
                        <span class="block mb-1">
                          <i class="ri-mail-line me-2 align-middle text-[.875rem] text-textmuted dark:text-textmuted inline-flex"></i>
                          {{ user.email }}
                        </span>
                      </div>
                    </td>
                    <td>
                      <span class="text-textmuted dark:text-textmuted">
                        {{ formatDate(user.created_at) }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-list">
                        <button 
                          aria-label="button" 
                          type="button" 
                          class="ti-btn btn-wave ti-btn-sm ti-btn-warning ti-btn-icon"
                          @click="showUserDetails(user)"
                          title="Visualizar"
                        >
                          <i class="ri-eye-line"></i>
                        </button>
                        <button 
                          aria-label="button" 
                          type="button" 
                          class="ti-btn btn-wave ti-btn-sm ti-btn-info ti-btn-icon"
                          @click="editUser(user)"
                          title="Editar"
                        >
                          <i class="ri-pencil-line"></i>
                        </button>
                        <button 
                          aria-label="button" 
                          type="button" 
                          class="ti-btn btn-wave ti-btn-sm ti-btn-danger ti-btn-icon"
                          @click="deleteUser(user)"
                          title="Excluir"
                        >
                          <i class="ri-delete-bin-line"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  
                  <!-- Empty State -->
                  <tr v-if="filteredData.length === 0">
                    <td colspan="7" class="text-center py-8">
                      <div class="text-textmuted dark:text-textmuted">
                        <i class="ri-search-line text-4xl mb-2"></i>
                        <p>Nenhum usuário encontrado</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pagination -->
          <div class="box-body border-t border-defaultborder">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <span class="text-sm text-textmuted dark:text-textmuted">Mostrar</span>
                  <select
                    v-model="itemsPerPage"
                    @change="handleItemsPerPageChange"
                    class="ti-form-select ti-form-select-sm"
                  >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                  </select>
                </div>
                
                <div class="text-sm text-textmuted dark:text-textmuted">
                  Mostrando {{ startIndex }} até {{ endIndex }} de {{ filteredData.length }} resultados
                </div>
              </div>

              <div class="flex items-center gap-2">
                <button
                  @click="goToPage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  class="ti-btn btn-wave ti-btn-outline-default ti-btn-sm"
                  :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                >
                  <i class="ri-arrow-left-s-line"></i>
                </button>
                
                <div class="flex items-center gap-1">
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      'ti-btn btn-wave ti-btn-sm',
                      page === currentPage 
                        ? 'ti-btn-primary' 
                        : 'ti-btn-outline-default'
                    ]"
                  >
                    {{ page }}
                  </button>
                </div>
                
                <button
                  @click="goToPage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  class="ti-btn btn-wave ti-btn-outline-default ti-btn-sm"
                  :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                >
                  <i class="ri-arrow-right-s-line"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End::row-1 -->

    <!-- Modals -->
    <CreateUserModal
      :show="showCreateModal"
      @close="showCreateModal = false"
      @success="handleUserCreated"
    />

    <UpdateUserModal
      :show="showUpdateModal"
      :user="selectedUser"
      @close="showUpdateModal = false"
      @success="handleUserUpdated"
    />

    <DeleteUserModal
      :show="showDeleteModal"
      :user="selectedUser"
      @close="showDeleteModal = false"
      @success="handleUserDeleted"
    />
  </AppLayout>
</template>

<script setup>
import CreateUserModal from '@/Components/Modals/CreateUserModal.vue'
import DeleteUserModal from '@/Components/Modals/DeleteUserModal.vue'
import UpdateUserModal from '@/Components/Modals/UpdateUserModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  users: Object
})

// Reactive state
const searchQuery = ref('')
const statusFilter = ref('')
const roleFilter = ref('')
const sortField = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const selectAll = ref(false)
const selectedUsers = ref([])

// Modal states
const showCreateModal = ref(false)
const showUpdateModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

// Computed properties
const filteredData = computed(() => {
  let result = [...props.users.data]

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(user => {
      return user.name.toLowerCase().includes(query) ||
             user.email.toLowerCase().includes(query)
    })
  }

  // Apply status filter
  if (statusFilter.value) {
    result = result.filter(user => user.status === statusFilter.value)
  }

  // Apply role filter
  if (roleFilter.value) {
    if (roleFilter.value === 'admin') {
      result = result.filter(user => user.is_master)
    } else if (roleFilter.value === 'user') {
      result = result.filter(user => !user.is_master)
    }
  }

  // Apply sorting
  if (sortField.value) {
    result.sort((a, b) => {
      let aValue, bValue
      
      switch (sortField.value) {
        case 'name':
          aValue = a.name.toLowerCase()
          bValue = b.name.toLowerCase()
          break
        case 'email':
          aValue = a.email.toLowerCase()
          bValue = b.email.toLowerCase()
          break
        case 'status':
          aValue = a.status
          bValue = b.status
          break
        case 'role':
          aValue = a.is_master ? 'admin' : 'user'
          bValue = b.is_master ? 'admin' : 'user'
          break
        case 'created_at':
          aValue = new Date(a.created_at)
          bValue = new Date(b.created_at)
          break
        default:
          return 0
      }
      
      if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
      if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return result
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredData.value.slice(start, end)
})

const startIndex = computed(() => {
  return filteredData.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1
})

const endIndex = computed(() => {
  return Math.min(currentPage.value * itemsPerPage.value, filteredData.value.length)
})

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(total)
    } else if (current >= total - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = total - 4; i <= total; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(total)
    }
  }
  
  return pages
})

// Methods
const getInitials = (name) => {
  if (!name) return ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const handleSearch = () => {
  currentPage.value = 1
}

const handleFilter = () => {
  currentPage.value = 1
}

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  currentPage.value = 1
}

const handleItemsPerPageChange = () => {
  currentPage.value = 1
}

const goToPage = (page) => {
  if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}


const handleSelectAll = () => {
  if (selectAll.value) {
    selectedUsers.value = paginatedData.value.map(user => user.id)
  } else {
    selectedUsers.value = []
  }
}

const showUserDetails = (user) => {
  selectedUser.value = user
  // Aqui você pode implementar um modal de detalhes ou navegar para uma página
  console.log('Visualizar usuário:', user)
}

const editUser = (user) => {
  selectedUser.value = user
  showUpdateModal.value = true
}

const deleteUser = (user) => {
  selectedUser.value = user
  showDeleteModal.value = true
}

const handleUserCreated = () => {
  showCreateModal.value = false
  window.location.reload()
}

const handleUserUpdated = () => {
  showUpdateModal.value = false
  window.location.reload()
}

const handleUserDeleted = () => {
  showDeleteModal.value = false
  window.location.reload()
}

const exportCSV = () => {
  const headers = ['Nome', 'Email', 'Status', 'Função', 'Data de Criação']
  const rows = filteredData.value.map(user => [
    user.name,
    user.email,
    user.status,
    user.is_master ? 'Administrador' : 'Usuário',
    formatDate(user.created_at)
  ])
  
  const csv = [headers, ...rows]
    .map(row => row.map(field => `"${field}"`).join(','))
    .join('\n')
  
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'usuarios.csv'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

// Watch for data changes
watch(() => props.users.data, () => {
  currentPage.value = 1
})

// Watch for selected users changes
watch(selectedUsers, (newValue) => {
  selectAll.value = newValue.length === paginatedData.value.length && newValue.length > 0
})
</script>

<style scoped>
/* Ynex Theme Styles - Following Original Pattern */
.box {
  background-color: rgb(var(--default-background));
  border: 1px solid rgb(var(--bootstrap-card-border));
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.dark .box {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.custom-box {
  border: 1px solid rgb(var(--bootstrap-card-border));
  border-radius: 0.5rem;
}

.dark .custom-box {
  border-color: rgb(var(--bootstrap-card-border));
}

.box-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid rgb(var(--bootstrap-card-border));
}

.dark .box-header {
  border-color: rgb(var(--bootstrap-card-border));
}

.box-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: rgb(var(--default-text-color));
}

.dark .box-title {
  color: rgb(var(--default-text-color));
}

.box-body {
  padding: 1.5rem;
}

.page-header {
  margin-bottom: 1.5rem;
}

.table {
  width: 100%;
  font-size: 0.875rem;
  text-align: left;
}

.table thead tr {
  background-color: rgb(var(--light));
}

.dark .table thead tr {
  background-color: rgb(var(--light));
}

.table th {
  padding: 0.75rem 1.5rem;
  font-weight: 500;
  color: rgb(var(--default-text-color));
}

.dark .table th {
  color: rgb(var(--default-text-color));
}

.table td {
  padding: 1rem 1.5rem;
  color: rgb(var(--default-text-color));
}

.dark .table td {
  color: rgb(var(--default-text-color));
}

.table tbody tr:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.dark .table tbody tr:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.crm-contact {
  border-bottom: 1px solid rgb(var(--bootstrap-card-border));
}

.dark .crm-contact {
  border-color: rgb(var(--bootstrap-card-border));
}

.crm-contact:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.dark .crm-contact:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.table-responsive {
  overflow-x: auto;
}

/* Form styles - Ynex Original */
.ti-form-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgb(var(--input-border));
  border-radius: 0.375rem;
  background-color: rgb(var(--default-background));
  color: rgb(var(--default-text-color));
  outline: none;
}

.ti-form-input:focus {
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
  border-color: rgb(var(--primary));
}

.dark .ti-form-input {
  background-color: rgb(var(--default-background));
  color: rgb(var(--default-text-color));
  border-color: rgb(var(--input-border));
}

.ti-form-select {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgb(var(--input-border));
  border-radius: 0.375rem;
  background-color: rgb(var(--default-background));
  color: rgb(var(--default-text-color));
  outline: none;
}

.ti-form-select:focus {
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
  border-color: rgb(var(--primary));
}

.dark .ti-form-select {
  background-color: rgb(var(--default-background));
  color: rgb(var(--default-text-color));
  border-color: rgb(var(--input-border));
}

.ti-form-select-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

/* Button styles */
.ti-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
  outline: none;
}

.ti-btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.ti-btn-primary {
  background-color: rgb(var(--primary));
  color: white;
}

.ti-btn-primary:hover {
  background-color: rgba(132, 90, 223, 0.9);
}

.ti-btn-primary-full {
  background-color: rgb(var(--primary));
  color: white;
}

.ti-btn-primary-full:hover {
  background-color: rgba(132, 90, 223, 0.9);
}

.ti-btn-success {
  background-color: rgb(var(--success));
  color: white;
}

.ti-btn-success:hover {
  background-color: rgba(38, 191, 148, 0.9);
}

.ti-btn-warning {
  background-color: rgb(var(--warning));
  color: white;
}

.ti-btn-warning:hover {
  background-color: rgba(245, 184, 73, 0.9);
}

.ti-btn-info {
  background-color: rgb(var(--info));
  color: white;
}

.ti-btn-info:hover {
  background-color: rgba(73, 182, 245, 0.9);
}

.ti-btn-danger {
  background-color: rgb(var(--danger));
  color: white;
}

.ti-btn-danger:hover {
  background-color: rgba(230, 83, 60, 0.9);
}

.ti-btn-light {
  background-color: rgb(var(--light));
  color: rgb(var(--default-text-color));
}

.ti-btn-light:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.dark .ti-btn-light {
  background-color: rgb(var(--light));
  color: rgb(var(--default-text-color));
}

.dark .ti-btn-light:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.ti-btn-outline-default {
  border: 1px solid rgb(var(--bootstrap-card-border));
  color: rgb(var(--default-text-color));
  background-color: transparent;
}

.ti-btn-outline-default:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.dark .ti-btn-outline-default {
  border-color: rgb(var(--bootstrap-card-border));
  color: rgb(var(--default-text-color));
}

.dark .ti-btn-outline-default:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.ti-btn-icon {
  padding: 0.5rem !important;
}

.btn-wave {
  position: relative;
  overflow: hidden;
}

.btn-list {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Dropdown styles */
.hs-dropdown {
  position: relative;
}

.ti-dropdown-menu {
  position: absolute;
  right: 0;
  margin-top: 0.5rem;
  width: 12rem;
  background-color: rgb(var(--default-background));
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(var(--bootstrap-card-border));
  z-index: 50;
}

.dark .ti-dropdown-menu {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.ti-dropdown-item {
  display: block;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: rgb(var(--default-text-color));
}

.ti-dropdown-item:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.dark .ti-dropdown-item {
  color: rgb(var(--default-text-color));
}

.dark .ti-dropdown-item:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

/* Badge styles */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.bg-light {
  background-color: rgb(var(--light));
  color: rgb(var(--default-text-color));
}

.dark .bg-light {
  background-color: rgb(var(--light));
  color: rgb(var(--default-text-color));
}

.text-defaulttextcolor {
  color: rgb(var(--default-text-color));
}

.dark .text-defaulttextcolor {
  color: rgb(var(--default-text-color));
}

.bg-success\/10 {
  background-color: rgba(38, 191, 148, 0.1);
  color: rgb(var(--success));
}

.bg-danger\/10 {
  background-color: rgba(230, 83, 60, 0.1);
  color: rgb(var(--danger));
}

.bg-warning\/10 {
  background-color: rgba(245, 184, 73, 0.1);
  color: rgb(var(--warning));
}

.bg-primary\/10 {
  background-color: rgba(132, 90, 223, 0.1);
  color: rgb(var(--primary));
}

/* Avatar styles */
.avatar {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.avatar-sm {
  width: 2rem;
  height: 2rem;
}

.avatar-rounded {
  border-radius: 50%;
}

/* Form check styles */
.form-check-input {
  width: 1rem;
  height: 1rem;
  color: rgb(var(--primary));
  background-color: rgb(var(--default-background));
  border: 1px solid rgb(var(--input-border));
  border-radius: 0.25rem;
}

.form-check-input:focus {
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
}

.dark .form-check-input {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--input-border));
}

/* Cursor pointer */
.cursor-pointer {
  cursor: pointer;
}

/* Search input dark mode fixes */
.dark .ti-form-input {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
  color: rgb(255 255 255) !important;
}

.dark .ti-form-input::placeholder {
  color: rgb(140 144 151) !important;
}

.dark .ti-form-input:focus {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(132 90 223) !important;
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2) !important;
}

.dark .ti-form-select {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
  color: rgb(255 255 255) !important;
}

.dark .ti-form-select:focus {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(132 90 223) !important;
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2) !important;
}

/* Search icon positioning fix */
.search-input-container {
  position: relative;
}

.search-input-container .search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  z-index: 2;
}

.search-input-container input {
  padding-left: 40px !important;
}

</style>