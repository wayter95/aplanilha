<template>
  <div class="box">
    <!-- Header -->
    <div class="box-header">
      <div class="flex items-center justify-between">
        <h5 class="box-title">{{ title }}</h5>
        <div class="flex items-center gap-2">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar..."
              class="ti-form-input"
              @input="handleSearch"
            />
            <i class="bx bx-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
          
          <!-- Export Buttons -->
          <div class="flex items-center gap-1">
            <button 
              @click="exportCSV"
              class="ti-btn btn-wave ti-btn-outline-primary ti-btn-sm"
              title="Exportar CSV"
            >
              <i class="bx bx-download"></i>
            </button>
            <button 
              @click="exportExcel"
              class="ti-btn btn-wave ti-btn-outline-success ti-btn-sm"
              title="Exportar Excel"
            >
              <i class="bx bx-file"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="box-body">
      <!-- Filters -->
      <div v-if="showFilters" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div v-for="filter in filters" :key="filter.key">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              {{ filter.label }}
            </label>
            <select
              v-model="filterValues[filter.key]"
              @change="handleFilter"
              class="ti-form-select"
            >
              <option value="">Todos</option>
              <option v-for="option in filter.options" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-auto table-bordered">
        <div class="ti-custom-table ti-striped-table ti-custom-table-hover">
          <table class="table whitespace-nowrap min-w-full">
            <thead>
              <tr class="border-b border-defaultborder">
                <th 
                  v-for="column in columns" 
                  :key="column.key"
                  scope="col" 
                  class="text-start cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                  @click="handleSort(column.key)"
                >
                  <div class="flex items-center gap-2">
                    <span>{{ column.label }}</span>
                    <div class="flex flex-col">
                      <i 
                        :class="[
                          'bx bx-chevron-up text-xs',
                          sortField === column.key && sortDirection === 'asc' 
                            ? 'text-primary' 
                            : 'text-gray-300 dark:text-gray-600'
                        ]"
                      ></i>
                      <i 
                        :class="[
                          'bx bx-chevron-down text-xs -mt-1',
                          sortField === column.key && sortDirection === 'desc' 
                            ? 'text-primary' 
                            : 'text-gray-300 dark:text-gray-600'
                        ]"
                      ></i>
                    </div>
                  </div>
                </th>
                <th v-if="hasActions" scope="col" class="text-start">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(row, rowIndex) in paginatedData" 
                :key="row.id || rowIndex" 
                class="border-b border-defaultborder hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td v-for="column in columns" :key="column.key">
                  <slot :name="`cell-${column.key}`" :value="getNestedValue(row, column.key)" :row="row" :column="column" :rowIndex="rowIndex">
                    <!-- Default cell content -->
                    <div v-if="column.type === 'avatar'">
                      <div class="flex items-center">
                        <div class="avatar avatar-sm me-2 avatar-rounded">
                          <div 
                            class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold"
                          >
                            {{ getInitials(getNestedValue(row, column.nameKey)) }}
                          </div>
                        </div>
                        <div v-if="column.showInfo">
                          <div class="leading-none">
                            <span>{{ getNestedValue(row, column.nameKey) }}</span>
                          </div>
                          <div class="leading-none">
                            <span class="text-[0.6875rem] text-[#8c9097] dark:text-white/50">
                              {{ getNestedValue(row, column.emailKey) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else-if="column.type === 'badge'">
                      <span 
                        :class="[
                          'badge',
                          column.badgeClass ? column.badgeClass(getNestedValue(row, column.key)) : 'bg-primary/10 text-primary'
                        ]"
                      >
                        {{ getNestedValue(row, column.key) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'date'">
                      <span class="text-[#8c9097] dark:text-white/50">
                        {{ formatDate(getNestedValue(row, column.key)) }}
                      </span>
                    </div>

                    <span v-else>{{ getNestedValue(row, column.key) }}</span>
                  </slot>
                </td>
                <td v-if="hasActions">
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
              
              <!-- Empty State -->
              <tr v-if="filteredData.length === 0">
                <td :colspan="columns.length + (hasActions ? 1 : 0)" class="text-center py-8">
                  <div class="text-gray-500 dark:text-gray-400">
                    <i class="bx bx-search text-4xl mb-2"></i>
                    <p>Nenhum resultado encontrado</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between mt-4">
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Mostrar</span>
            <select
              v-model="itemsPerPage"
              @change="handleItemsPerPageChange"
              class="ti-form-select ti-form-select-sm"
            >
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-600 dark:text-gray-400">itens por página</span>
          </div>
          
          <div class="text-sm text-gray-600 dark:text-gray-400">
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
            <i class="bx bx-chevron-left"></i>
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
            <i class="bx bx-chevron-right"></i>
          </button>
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
  }
})

const emit = defineEmits(['action'])

// Reactive state
const searchQuery = ref('')
const sortField = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const filterValues = ref({})

// Initialize filter values
props.filters.forEach(filter => {
  filterValues.value[filter.key] = ''
})

// Computed properties
const hasActions = computed(() => props.actions.length > 0)

const filteredData = computed(() => {
  let result = [...props.data]

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(row => {
      return props.columns.some(column => {
        const value = getNestedValue(row, column.key)
        return String(value).toLowerCase().includes(query)
      })
    })
  }

  // Apply filters
  props.filters.forEach(filter => {
    const filterValue = filterValues.value[filter.key]
    if (filterValue) {
      result = result.filter(row => {
        const value = getNestedValue(row, filter.key)
        return String(value) === filterValue
      })
    }
  })

  // Apply sorting
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

// Methods
const getNestedValue = (obj, key) => {
  return key.split('.').reduce((acc, part) => acc && acc[part], obj)
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
}

const handleFilter = () => {
  currentPage.value = 1
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

const exportCSV = () => {
  const headers = props.columns.map(col => col.label).join(',')
  const rows = filteredData.value.map(row => 
    props.columns.map(col => `"${getNestedValue(row, col.key)}"`).join(',')
  ).join('\n')
  
  const csv = `${headers}\n${rows}`
  downloadFile(csv, 'usuarios.csv', 'text/csv')
}

const exportExcel = () => {
  // Simple CSV export as Excel (for now)
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

// Watch for data changes
watch(() => props.data, () => {
  currentPage.value = 1
})
</script>

<style scoped>
/* Ynex theme styles */
.box {
  @apply bg-white dark:bg-bodybg border border-defaultborder rounded-lg shadow-sm;
}

.box-header {
  @apply px-6 py-4 border-b border-defaultborder;
}

.box-title {
  @apply text-lg font-semibold text-defaulttextcolor dark:text-defaulttextcolor/70;
}

.box-body {
  @apply p-6;
}

.table {
  @apply w-full text-sm text-left;
}

.table thead tr {
  @apply bg-gray-50 dark:bg-gray-800;
}

.table th {
  @apply px-6 py-3 font-medium text-gray-900 dark:text-white;
}

.table td {
  @apply px-6 py-4 text-gray-700 dark:text-gray-300;
}

.table tbody tr:hover {
  @apply bg-gray-50 dark:bg-gray-700/50;
}

.ti-custom-table {
  @apply border border-defaultborder rounded-lg overflow-hidden;
}

.ti-striped-table tbody tr:nth-child(even) {
  @apply bg-gray-50/50 dark:bg-gray-800/50;
}

.ti-custom-table-hover tbody tr:hover {
  @apply bg-primary/5 dark:bg-primary/10;
}

.table-bordered {
  @apply border border-defaultborder rounded-lg;
}

/* Form styles */
.ti-form-input {
  @apply w-full px-3 py-2 border border-defaultborder rounded-md bg-white dark:bg-bodybg text-defaulttextcolor dark:text-defaulttextcolor/70 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary;
}

.ti-form-select {
  @apply w-full px-3 py-2 border border-defaultborder rounded-md bg-white dark:bg-bodybg text-defaulttextcolor dark:text-defaulttextcolor/70 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary;
}

.ti-form-select-sm {
  @apply px-2 py-1 text-sm;
}

/* Button styles */
.ti-btn {
  @apply inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.ti-btn-sm {
  @apply px-3 py-1.5 text-xs;
}

.ti-btn-primary {
  @apply bg-primary text-white hover:bg-primary/90 focus:ring-primary;
}

.ti-btn-outline-primary {
  @apply border border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary;
}

.ti-btn-outline-success {
  @apply border border-success text-success hover:bg-success hover:text-white focus:ring-success;
}

.ti-btn-outline-default {
  @apply border border-defaultborder text-defaulttextcolor dark:text-defaulttextcolor/70 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-gray-500;
}

.btn-wave {
  @apply relative overflow-hidden;
}

/* Badge styles */
.badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.bg-primary\/10 {
  @apply bg-primary/10 text-primary;
}

.bg-success\/10 {
  @apply bg-success/10 text-success;
}

.bg-danger\/10 {
  @apply bg-danger/10 text-danger;
}

.bg-warning\/10 {
  @apply bg-warning/10 text-warning;
}

/* Avatar styles */
.avatar {
  @apply relative inline-flex items-center justify-center;
}

.avatar-sm {
  @apply w-8 h-8;
}

.avatar-rounded {
  @apply rounded-full;
}

/* Colors */
.text-primary {
  @apply text-primary;
}

.text-success {
  @apply text-success;
}

.text-danger {
  @apply text-danger;
}

.text-warning {
  @apply text-warning;
}

.bg-primary {
  @apply bg-primary;
}

.bg-success {
  @apply bg-success;
}

.bg-danger {
  @apply bg-danger;
}

.bg-warning {
  @apply bg-warning;
}
</style>
