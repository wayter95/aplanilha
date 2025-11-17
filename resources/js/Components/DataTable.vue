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
        </div>
      </div>
    </div>

    <div v-if="showFilters || showSearch" class="box-body !p-0 border-b border-defaultborder">
      <div class="p-3">
        <div class="flex flex-wrap items-center gap-2">
          <div v-if="showSearch" class="flex-1 min-w-[200px]">
            <div class="search-input-container">
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="searchPlaceholder"
                class="ti-form-input !py-1.5 !text-[0.75rem]"
                @input="handleSearch"
              />
              <i class="ri-search-line search-icon text-[#8c9097] dark:text-white/50 !text-[0.875rem]"></i>
            </div>
          </div>
          
          <div v-for="filter in filters" :key="filter.key" class="min-w-[160px]">
            <select
              v-model="filterValues[filter.key]"
              @change="handleFilter"
              class="ti-form-select !py-1.5 !text-[0.75rem]"
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
        <div class="ti-custom-table ti-custom-table-hover">
          <table class="table whitespace-nowrap min-w-full !text-[0.75rem]">
            <thead>
              <tr class="border-b border-defaultborder">
                <th v-if="showSelectAll" scope="col" class="text-start !py-2 !px-3">
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
                  class="text-start !py-2 !px-3 !text-[0.75rem] !font-medium"
                  :class="{ 'cursor-pointer': column.sortable !== false }"
                  @click="column.sortable !== false ? handleSort(column.key) : null"
                >
                  <div class="flex items-center gap-2">
                    <span>{{ column.label }}</span>
                    <i v-if="column.sortable !== false" class="ri-arrow-up-down-line text-[#8c9097] dark:text-white/50 !text-[0.75rem]"></i>
                  </div>
                </th>
                
                <th v-if="hasActions" scope="col" :class="[actionsAlignClass, '!py-2 !px-3 !text-[0.75rem] !font-medium']">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(row, rowIndex) in paginatedData" 
                :key="row.id || rowIndex" 
                class="crm-contact border-b border-defaultborder"
              >
                <td v-if="showSelectAll" class="text-start !py-2 !px-3">
                  <input
                    type="checkbox"
                    :checked="selectedItems.includes(row.id)"
                    @change="toggleRowSelection(row.id)"
                    class="form-check-input"
                  >
                </td>
                
                <td v-for="column in columns" :key="column.key" class="text-start !py-2 !px-3">
                  <slot :name="`cell-${column.key}`" :value="getNestedValue(row, column.key)" :row="row" :column="column" :rowIndex="rowIndex">
                    <div v-if="column.type === 'user'">
                      <div class="flex items-center gap-2">
                        <span class="avatar avatar-xs avatar-rounded">
                          <img 
                            v-if="getUserPhoto(row, column.photoKey)"
                            :src="getUserPhoto(row, column.photoKey)"
                            :alt="getNestedValue(row, column.nameKey || 'name')"
                            @error="handleImageError"
                          />
                          <div 
                            v-else
                            class="w-7 h-7 bg-primary/10 text-primary flex items-center justify-center !text-[0.65rem] font-semibold"
                            style="border-radius: 50%;"
                          >
                            {{ getInitials(getNestedValue(row, column.nameKey || 'name')) }}
                          </div>
                        </span>
                        <div class="leading-tight">
                          <div class="leading-none mb-0.5">
                            <span class="font-semibold !text-[0.75rem]">{{ getNestedValue(row, column.nameKey || 'name') }}</span>
                          </div>
                          <div class="leading-none">
                            <span class="!text-[0.65rem] text-[#8c9097] dark:text-white/50">
                              {{ getNestedValue(row, column.emailKey || 'email') }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else-if="column.type === 'status'">
                      <span 
                        :class="[
                          'badge !text-[0.65rem] !py-0.5 !px-2',
                          getStatusBadgeClass(getStatusValue(row, column.key))
                        ]"
                      >
                        {{ getStatusValue(row, column.key) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'role'">
                      <span 
                        :class="[
                          'badge !text-[0.65rem] !py-0.5 !px-2',
                          getRoleBadgeClass(getNestedValue(row, column.key))
                        ]"
                      >
                        {{ getRoleLabel(getNestedValue(row, column.key)) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'date'">
                      <span class="text-[#8c9097] dark:text-white/50 !text-[0.75rem]">
                        {{ formatDate(getNestedValue(row, column.key)) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'badge'">
                      <span 
                        :class="[
                          'badge !text-[0.65rem] !py-0.5 !px-2',
                          column.badgeClass ? column.badgeClass(getNestedValue(row, column.key)) : 'bg-primary/10 text-primary'
                        ]"
                      >
                        {{ column.key === 'permissions' ? (getNestedValue(row, column.key)?.length || 0) + ' permissões' : getNestedValue(row, column.key) }}
                      </span>
                    </div>

                    <div v-else-if="column.type === 'permissions_count'">
                      <span class="badge bg-info/10 text-info !text-[0.65rem] !py-0.5 !px-2">
                        {{ getNestedValue(row, column.key)?.length || 0 }} permissões
                      </span>
                    </div>

                    <span v-else class="!text-[0.75rem]">{{ getNestedValue(row, column.key) }}</span>
                  </slot>
                </td>
                
                <td v-if="hasActions" :class="[actionsAlignClass, '!py-2 !px-3']">
                  <slot name="cell-actions" :row="row" :rowIndex="rowIndex">
                    <div :class="['flex items-center gap-1', actionsAlignClass]">
                      <button 
                        v-for="action in actions" 
                        :key="action.name"
                        @click="handleAction(action.name, row, rowIndex)"
                        :class="[
                          'ti-btn ti-btn-icon ti-btn-sm',
                          action.class || 'ti-btn-primary-full'
                        ]"
                        :title="action.label"
                      >
                        <i v-if="action.icon" :class="action.icon"></i>
                      </button>
                    </div>
                  </slot>
                </td>
              </tr>
              
              <tr v-if="filteredData.length === 0">
                <td :colspan="columns.length + (hasActions ? 1 : 0) + (showSelectAll ? 1 : 0)" class="text-center !py-8">
                  <div class="py-8">
                    <div class="flex flex-col items-center justify-center">
                      <!-- Ícone -->
                      <div class="mb-4">
                        <div class="w-16 h-16 bg-light dark:bg-bodybg rounded-full flex items-center justify-center">
                          <i class="ri-search-line !text-3xl text-[#8c9097] dark:text-white/50 opacity-50"></i>
                        </div>
                      </div>
                      
                      <!-- Título -->
                      <h3 class="!text-[0.938rem] font-semibold text-defaulttextcolor dark:text-white mb-1">
                        Nenhum resultado encontrado
                      </h3>
                      
                      <!-- Descrição -->
                      <p class="!text-[0.75rem] text-[#8c9097] dark:text-white/50 max-w-md text-center mb-4">
                        <span v-if="searchQuery || Object.values(filterValues).some(v => v)">
                          Não encontramos registros. Tente ajustar os filtros.
                        </span>
                        <span v-else>
                          Ainda não há registros cadastrados.
                        </span>
                      </p>
                      
                      <!-- Ações -->
                      <div class="flex gap-2">
                        <button 
                          v-if="searchQuery || Object.values(filterValues).some(v => v)"
                          @click="clearFilters"
                          class="ti-btn ti-btn-outline-primary !py-1 !px-3 !text-[0.75rem]"
                        >
                          <i class="ri-refresh-line me-1 !text-[0.875rem]"></i>
                          Limpar filtros
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="box-body !p-0 border-t border-defaultborder">
        <div class="tabulator">
          <div class="tabulator-footer">
            <div class="tabulator-footer-contents">
            <span class="tabulator-page-counter">
              <span>{{ startIndex }}-{{ endIndex }} de {{ filteredData.length }}</span>
            </span>
            
            <span class="tabulator-paginator">
              <label>Registros por página</label>
              <select
                v-model="itemsPerPage"
                @change="handleItemsPerPageChange"
                class="tabulator-page-size"
                aria-label="Page Size"
                title="Page Size"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="tabulator-page"
                type="button"
                data-page="prev"
              >
                ‹
              </button>
              
              <span class="tabulator-pages">
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="['tabulator-page', { 'active': page === currentPage }]"
                  type="button"
                  role="button"
                  :aria-label="`Show Page ${page}`"
                  :title="`Show Page ${page}`"
                  :data-page="page"
                >
                  {{ page }}
                </button>
              </span>
              
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="tabulator-page"
                type="button"
                data-page="next"
              >
                ›
              </button>
            </span>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from './Button.vue'
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { computed, ref, watch } from 'vue'

// Composables
const { getPhotoUrl } = usePhotoUrl()

// Estado para URLs de fotos carregadas
const photoUrls = ref(new Map())

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
  },
  actionsAlign: {
    type: String,
    default: 'center',
    validator: (value) => ['left', 'center', 'right'].includes(value)
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

const actionsAlignClass = computed(() => {
  const alignMap = {
    'left': 'text-start justify-start',
    'center': 'text-center justify-center',
    'right': 'text-end justify-end'
  }
  return alignMap[props.actionsAlign] || alignMap.center
})

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

const getUserPhoto = (row, photoKey) => {
  if (!photoKey) return null
  
  const photoValue = getNestedValue(row, photoKey)
  if (!photoValue) return null
  
  // Se é uma URL completa, usar diretamente
  if (photoValue.startsWith('http')) {
    return photoValue
  }
  
  if (photoUrls.value.has(photoValue)) {
    return photoUrls.value.get(photoValue)
  }
  
  getPhotoUrl(photoValue).then(url => {
    if (url) {
      photoUrls.value.set(photoValue, url)
    }
  })
  
  return null
}

const handleImageError = (event) => {
  // Se a imagem falhar ao carregar, esconder a img e mostrar as iniciais
  event.target.style.display = 'none'
  const parent = event.target.parentElement
  const fallback = parent.querySelector('.fallback-initials')
  if (fallback) {
    fallback.style.display = 'flex'
  }
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

const clearFilters = () => {
  searchQuery.value = ''
  Object.keys(filterValues.value).forEach(key => {
    filterValues.value[key] = ''
  })
  currentPage.value = 1
  emit('search-change', '')
  emit('filter-change', {})
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
/* Search icon positioning */
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

/* Remove icons from pagination number buttons */
.pagination-number::before,
.pagination-number::after,
.pagination-number i {
  display: none !important;
  content: none !important;
}

.pagination-number span {
  display: inline-block;
}

/* Items per page select - ensure dropdown arrow is visible */
.items-per-page-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%238c9097' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 10px;
  min-width: 60px;
}
</style>
