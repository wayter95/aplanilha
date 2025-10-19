<template>
  <div class="box custom-box">
    <div class="box-header">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="box-title">
          {{ title }}
          <span v-if="showCount" class="badge bg-light text-defaulttextcolor rounded-full ms-1 text-[0.75rem] align-middle">
            {{ filteredData.length }}
          </span>
        </div>
        <div class="flex flex-wrap gap-2">
          <slot name="header-actions"></slot>
          
          <button 
            v-if="showExport"
            @click="exportCSV"
            class="ti-btn btn-wave ti-btn-success !py-1 !px-2 !text-[0.75rem] !m-0"
          >
            Exportar CSV
          </button>
        </div>
      </div>
    </div>

    <div v-if="showFilters || showSearch" class="box-body !p-0 border-b border-defaultborder">
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div v-if="showSearch" class="md:col-span-2">
            <div class="search-input-container">
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="searchPlaceholder"
                class="ti-form-input pr-4"
                @input="handleSearch"
              />
              <i class="ri-search-line search-icon text-textmuted dark:text-textmuted text-sm"></i>
            </div>
          </div>
          
          <div v-for="filter in filters" :key="filter.key">
            <select
              v-model="filterValues[filter.key]"
              @change="handleFilter"
              class="ti-form-select"
            >
              <option value="">{{ getFilterPlaceholder(filter) }}</option>
              <option v-for="option in filter.options" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="box-body !p-0">
      <div class="overflow-auto">
        <div class="ti-custom-table ti-striped-table ti-custom-table-hover">
          <table class="table whitespace-nowrap min-w-full">
            <thead>
              <tr class="border-b border-defaultborder">
                <th v-if="showSelectAll" scope="col" class="text-start">
                  <input
                    type="checkbox"
                    :checked="selectAll"
                    @change="handleSelectAll"
                    class="form-check-input"
                  >
                </th>
                
                <th 
                  v-for="column in columns" 
                  :key="column.key"
                  scope="col" 
                  class="text-start cursor-pointer"
                  @click="column.sortable !== false ? handleSort(column.key) : null"
                  :class="{ 'cursor-pointer': column.sortable !== false }"
                >
                  <div class="flex items-center gap-2">
                    <span>{{ column.label }}</span>
                    <i v-if="column.sortable !== false" class="ri-arrow-up-down-line text-textmuted dark:text-textmuted"></i>
                  </div>
                </th>
                
                <th v-if="hasActions" scope="col" class="text-start">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(row, rowIndex) in paginatedData" 
                :key="row.id || rowIndex" 
                class="crm-contact"
              >
                <td v-if="showSelectAll" class="text-start">
                  <input
                    type="checkbox"
                    :checked="selectedItems.includes(row.id)"
                    @change="toggleRowSelection(row.id)"
                    class="form-check-input"
                  >
                </td>
                
                <td v-for="column in columns" :key="column.key" class="text-start">
                  <slot :name="`cell-${column.key}`" :value="getNestedValue(row, column.key)" :row="row" :column="column" :rowIndex="rowIndex">
                    <div v-if="column.type === 'user'">
                      <div class="flex items-center gap-3">
                        <div class="avatar avatar-sm avatar-rounded">
                          <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold">
                            {{ getInitials(getNestedValue(row, column.nameKey || 'name')) }}
                          </div>
                        </div>
                        <div>
                          <div class="leading-none">
                            <span class="font-semibold">{{ getNestedValue(row, column.nameKey || 'name') }}</span>
                          </div>
                          <div class="leading-none">
                            <span class="text-[0.6875rem] text-textmuted dark:text-textmuted">
                              {{ getNestedValue(row, column.emailKey || 'email') }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else-if="column.type === 'status'">
                      <span 
                        :class="[
                          'badge',
                          getStatusBadgeClass(getStatusValue(row, column.key))
                        ]"
                      >
                        {{ getStatusValue(row, column.key) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'role'">
                      <span 
                        :class="[
                          'badge',
                          getRoleBadgeClass(getNestedValue(row, column.key))
                        ]"
                      >
                        {{ getRoleLabel(getNestedValue(row, column.key)) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'date'">
                      <span class="text-textmuted dark:text-textmuted">
                        {{ formatDate(getNestedValue(row, column.key)) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'badge'">
                      <span 
                        :class="[
                          'badge',
                          column.badgeClass ? column.badgeClass(getNestedValue(row, column.key)) : 'bg-primary/10 text-primary'
                        ]"
                      >
                        {{ column.key === 'permissions' ? (getNestedValue(row, column.key)?.length || 0) + ' permissões' : getNestedValue(row, column.key) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'permissions_count'">
                      <span class="badge bg-info/10 text-info">
                        {{ getNestedValue(row, column.key)?.length || 0 }} permissões
                      </span>
                    </div>

                    <span v-else>{{ getNestedValue(row, column.key) }}</span>
                  </slot>
                </td>
                
                <td v-if="hasActions" class="text-start">
                  <div class="flex items-center gap-2">
                    <button 
                      v-for="action in actions" 
                      :key="action.name"
                      @click="handleAction(action.name, row, rowIndex)"
                      :class="[
                        'ti-btn btn-wave ti-btn-sm',
                        action.class || 'ti-btn-outline-primary'
                      ]"
                      :title="action.label"
                    >
                      <i v-if="action.icon" :class="action.icon"></i>
                      <span v-if="action.showLabel">{{ action.label }}</span>
                    </button>
                  </div>
                </td>
              </tr>
              
              <tr v-if="filteredData.length === 0">
                <td :colspan="columns.length + (hasActions ? 1 : 0) + (showSelectAll ? 1 : 0)" class="text-center py-8">
                  <div class="text-textmuted dark:text-textmuted">
                    <i class="ri-search-line text-4xl mb-2"></i>
                    <p>Nenhum resultado encontrado</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="box-body !p-0">
        <div class="flex items-center justify-between p-4">
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <select
                v-model="itemsPerPage"
                @change="handleItemsPerPageChange"
                class="ti-form-select !py-1 !px-2 !text-[0.75rem] !m-0"
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
              class="ti-btn btn-wave ti-btn-outline-default !py-1 !px-2 !text-[0.75rem] !m-0"
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
                  'ti-btn btn-wave !py-1 !px-2 !text-[0.75rem] !m-0',
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
              class="ti-btn btn-wave ti-btn-outline-default !py-1 !px-2 !text-[0.75rem] !m-0"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
            >
              <i class="ri-arrow-right-s-line"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'DataTable'
  },
  data: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  actions: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Array,
    default: () => []
  },
  showFilters: {
    type: Boolean,
    default: true
  },
  serverSideFiltering: {
    type: Boolean,
    default: false
  },
  initialFilters: {
    type: Object,
    default: () => ({})
  },
  initialSearch: {
    type: String,
    default: ''
  },
  showSearch: {
    type: Boolean,
    default: true
  },
  showExport: {
    type: Boolean,
    default: true
  },
  showSelectAll: {
    type: Boolean,
    default: false
  },
  showCount: {
    type: Boolean,
    default: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar...'
  }
})

const emit = defineEmits(['action', 'selection-change', 'filter-change', 'search-change'])

// Reactive state
const searchQuery = ref(props.initialSearch || '')
const sortField = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const filterValues = ref({ ...props.initialFilters })
const selectAll = ref(false)
const selectedItems = ref([])

props.filters.forEach(filter => {
  if (!filterValues.value.hasOwnProperty(filter.key)) {
    filterValues.value[filter.key] = ''
  }
})

const hasActions = computed(() => props.actions.length > 0)

const filteredData = computed(() => {
  let result = [...props.data]

  if (props.serverSideFiltering) {
    if (sortField.value) {
      result.sort((a, b) => {
        const aVal = getNestedValue(a, sortField.value)
        const bVal = getNestedValue(b, sortField.value)
        const modifier = sortDirection.value === 'asc' ? 1 : -1
        return aVal > bVal ? modifier : aVal < bVal ? -modifier : 0
      })
    }
    return result
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(row => {
      return props.columns.some(column => {
        const value = getNestedValue(row, column.key)
        return String(value).toLowerCase().includes(query)
      })
    })
  }

  props.filters.forEach(filter => {
    const filterValue = filterValues.value[filter.key]
    if (filterValue) {
      result = result.filter(row => {
        const value = getNestedValue(row, filter.key)
        return String(value) === filterValue
      })
    }
  })

  if (sortField.value) {
    result.sort((a, b) => {
      const aValue = getNestedValue(a, sortField.value)
      const bValue = getNestedValue(b, sortField.value)
      
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

const getNestedValue = (obj, key) => {
  return key.split('.').reduce((acc, part) => {
    if (!acc) return acc
    
    if (part.includes('[') && part.includes(']')) {
      const arrayName = part.substring(0, part.indexOf('['))
      const index = parseInt(part.substring(part.indexOf('[') + 1, part.indexOf(']')))
      return acc[arrayName] && acc[arrayName][index]
    }
    
    return acc[part]
  }, obj)
}

const getStatusValue = (obj, key) => {
  const value = getNestedValue(obj, key)
  if (key === 'is_active') {
    return value ? 'Ativo' : 'Inativo'
  }
  return value
}

const getInitials = (name) => {
  if (!name) return ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('pt-BR')
}

const handleSearch = () => {
  currentPage.value = 1
  emit('search-change', searchQuery.value)
}

const handleFilter = () => {
  currentPage.value = 1
  emit('filter-change', filterValues.value)
}

const handleSort = (field) => {
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
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const handleAction = (actionName, row, rowIndex) => {
  emit('action', { action: actionName, row, rowIndex })
}

const handleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = paginatedData.value.map(row => row.id)
  } else {
    selectedItems.value = []
  }
  emit('selection-change', selectedItems.value)
}

const toggleRowSelection = (rowId) => {
  const index = selectedItems.value.indexOf(rowId)
  if (index > -1) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(rowId)
  }
  
  selectAll.value = selectedItems.value.length === paginatedData.value.length
  emit('selection-change', selectedItems.value)
}

const getStatusBadgeClass = (status) => {
  const statusMap = {
    'Ativo': 'bg-success/10 text-success',
    'Inativo': 'bg-danger/10 text-danger',
    'Pendente': 'bg-warning/10 text-warning',
    'Bloqueado': 'bg-danger/10 text-danger'
  }
  return statusMap[status] || 'bg-light text-defaulttextcolor'
}

const getRoleBadgeClass = (role) => {
  if (!role) return 'bg-light text-defaulttextcolor'
  
  const roleMap = {
    'admin': 'bg-primary/10 text-primary',
    'user': 'bg-success/10 text-success',
    'moderator': 'bg-warning/10 text-warning',
    'guest': 'bg-light text-defaulttextcolor',
    'Administrador': 'bg-primary/10 text-primary',
    'Usuário': 'bg-success/10 text-success',
    'Moderador': 'bg-warning/10 text-warning',
    'Convidado': 'bg-light text-defaulttextcolor'
  }
  return roleMap[role] || 'bg-light text-defaulttextcolor'
}

const getRoleLabel = (role) => {
  if (!role) return 'N/A'
  
  const roleMap = {
    'admin': 'Administrador',
    'user': 'Usuário',
    'moderator': 'Moderador',
    'guest': 'Convidado'
  }
  
  if (Object.values(roleMap).includes(role)) {
    return role
  }
  
  return roleMap[role] || role
}

const getFilterPlaceholder = (filter) => {
  const placeholders = {
    'status': 'Selecione o status',
    'role': 'Selecione a função',
    'search': 'Digite para buscar...',
    'type': 'Selecione o tipo',
    'category': 'Selecione a categoria'
  }
  
  return placeholders[filter.key] || `Selecione ${filter.label.toLowerCase()}`
}

const exportCSV = () => {
  const headers = props.columns.map(col => col.label).join(',')
  const rows = filteredData.value.map(row => 
    props.columns.map(col => `"${getNestedValue(row, col.key)}"`).join(',')
  ).join('\n')
  
  const csv = `${headers}\n${rows}`
  downloadFile(csv, 'usuarios.csv', 'text/csv')
}

const exportExcel = () => {
  exportCSV()
}

const downloadFile = (content, filename, mimeType) => {
  const blob = new Blob([content], { type: mimeType })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

watch(() => props.data, () => {
  currentPage.value = 1
})
</script>

<style scoped>
.box {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.custom-box {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.box-header {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.box-title {
  color: rgb(var(--default-text-color));
}

.table {
  background-color: rgb(var(--default-background));
}

.table thead tr {
  background-color: rgb(var(--light));
}

.table th {
  color: rgb(var(--default-text-color));
}

.table td {
  color: rgb(var(--default-text-color));
}

.table tbody tr:nth-child(even) {
  background-color: rgb(var(--light));
}

.crm-contact {
  border-color: rgb(var(--bootstrap-card-border));
}

.crm-contact:hover {
  background-color: rgb(var(--list-hover-focus-bg));
}

.ti-form-input {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--input-border));
  color: rgb(var(--default-text-color));
}

.ti-form-input:focus {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--primary));
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
}

.ti-form-select {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--input-border));
  color: rgb(var(--default-text-color));
}

.ti-form-select:focus {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--primary));
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
}

.ti-btn-light {
  background-color: rgb(var(--light));
  border-color: rgb(var(--default-border));
  color: rgb(var(--default-text-color));
}

.ti-dropdown-item {
  color: rgb(var(--default-text-color));
}

.bg-light {
  background-color: rgb(var(--light));
}

.form-check-input {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--input-border));
}

.form-check-input:focus {
  background-color: rgb(var(--form-control-bg));
  border-color: rgb(var(--primary));
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2);
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

/* Dark mode styles */
.dark .box {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
}

.dark .custom-box {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
}

.dark .box-header {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
}

.dark .box-title {
  color: rgb(255 255 255) !important;
}

.dark .table {
  background-color: rgb(35 38 40) !important;
}

.dark .table thead tr {
  background-color: rgb(43 46 49) !important;
}

.dark .table th {
  color: rgb(255 255 255) !important;
}

.dark .table td {
  color: rgb(255 255 255) !important;
}

.dark .crm-contact {
  border-color: rgb(49 51 53) !important;
}

.dark .crm-contact:hover {
  background-color: rgb(43 46 49) !important;
}

.dark .ti-dropdown-menu {
  border-color: rgb(49 51 53) !important;
}

.dark .ti-btn-outline-default {
  border-color: rgb(49 51 53) !important;
}

.dark .ti-btn-light {
  background-color: rgb(43 46 49) !important;
  border-color: rgb(49 51 53) !important;
}

.dark .ti-dropdown-item {
  color: rgb(255 255 255) !important;
}

.dark .bg-light {
  background-color: rgb(43 46 49) !important;
}

.dark .form-check-input {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(49 51 53) !important;
}

.dark .form-check-input:focus {
  background-color: rgb(35 38 40) !important;
  border-color: rgb(132 90 223) !important;
  box-shadow: 0 0 0 2px rgba(132, 90, 223, 0.2) !important;
}
</style>
