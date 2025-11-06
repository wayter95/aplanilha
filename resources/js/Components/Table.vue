<template>
  <div class="table-responsive">
    <table class="table whitespace-nowrap table-hover min-w-full ti-custom-table-hover">
      <thead>
        <tr class="border-b border-defaultborder">
          <th 
            v-for="column in columns" 
            :key="column.key"
            scope="col" 
            class="text-start"
            :class="column.headerClass"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="(row, rowIndex) in data" 
          :key="rowIndex"
          class="border-b border-defaultborder"
          :class="getRowClass(row, rowIndex)"
        >
          <td v-for="column in columns" :key="column.key">
            <slot 
              :name="`cell-${column.key}`" 
              :row="row" 
              :value="getNestedValue(row, column.key)"
              :column="column"
              :rowIndex="rowIndex"
            >
              <div v-if="column.type === 'avatar'">
                <div class="flex items-center">
                  <div class="avatar avatar-sm me-2 avatar-rounded">
                    <div 
                      class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold"
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
                    getBadgeClass(getNestedValue(row, column.key), column.badgeOptions)
                  ]"
                >
                  {{ getNestedValue(row, column.key) }}
                </span>
              </div>

              <div v-else-if="column.type === 'progress'">
                <div class="progress progress-xs">
                  <div 
                    class="progress-bar bg-primary"
                    :style="{ width: getNestedValue(row, column.key) + '%' }"
                  ></div>
                </div>
              </div>

              <div v-else-if="column.type === 'avatar-list'">
                <div class="avatar-list-stacked">
                  <span 
                    v-for="(avatar, index) in getNestedValue(row, column.key)" 
                    :key="index"
                    class="avatar avatar-sm avatar-rounded"
                  >
                    <img v-if="avatar.image" :src="avatar.image" :alt="avatar.name">
                    <div v-else class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-xs">
                      {{ getInitials(avatar.name) }}
                    </div>
                  </span>
                  <a 
                    v-if="getNestedValue(row, column.remainingKey)"
                    class="avatar avatar-sm bg-primary text-white avatar-rounded"
                    href="javascript:void(0);"
                  >
                    +{{ getNestedValue(row, column.remainingKey) }}
                  </a>
                </div>
              </div>

              <div v-else-if="column.type === 'actions'">
                <div class="flex items-center space-x-2">
                  <button
                    v-for="action in column.actions"
                    :key="action.name"
                    @click="handleAction(action.name, row, rowIndex)"
                    :class="[
                      'btn btn-sm',
                      action.class || 'btn-outline-primary'
                    ]"
                    :title="action.title"
                  >
                    <i :class="action.icon" v-if="action.icon"></i>
                    <span v-if="action.label">{{ action.label }}</span>
                  </button>
                </div>
              </div>

              <span v-else>{{ getNestedValue(row, column.key) }}</span>
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!data || data.length === 0" class="text-center py-8">
      <div class="text-gray-500 dark:text-gray-400">
        <i class="bx bx-table text-4xl mb-2"></i>
        <p>{{ emptyMessage || 'Nenhum registro encontrado' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>

const props = defineProps({
  columns: {
    type: Array,
    required: true,
    validator: (columns) => {
      return columns.every(col => col.key && col.label)
    }
  },
  data: {
    type: Array,
    default: () => []
  },
  emptyMessage: {
    type: String,
    default: 'Nenhum registro encontrado'
  },
  rowClass: {
    type: [String, Function],
    default: null
  }
})

const emit = defineEmits(['action'])

const getNestedValue = (obj, path) => {
  if (!path) return ''
  return path.split('.').reduce((current, key) => {
    return current && current[key] !== undefined ? current[key] : ''
  }, obj)
}

const getInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getBadgeClass = (value, badgeOptions = {}) => {
  if (typeof badgeOptions === 'function') {
    return badgeOptions(value)
  }
  
  if (typeof badgeOptions === 'object' && badgeOptions[value]) {
    return badgeOptions[value]
  }

  const defaultClasses = {
    'Ativo': 'bg-success/10 text-success',
    'Inativo': 'bg-danger/10 text-danger',
    'Pendente': 'bg-warning/10 text-warning',
    'Concluído': 'bg-primary/10 text-primary'
  }

  return defaultClasses[value] || 'bg-primary/10 text-primary'
}

const getRowClass = (row, rowIndex) => {
  if (typeof props.rowClass === 'function') {
    return props.rowClass(row, rowIndex)
  }
  return props.rowClass || ''
}

const handleAction = (actionName, row, rowIndex) => {
  emit('action', { action: actionName, row, rowIndex })
}
</script>

<style scoped>
.table {
  width: 100%;
  font-size: 0.875rem;
  text-align: left;
  color: #6b7280;
}

.dark .table {
  color: #9ca3af;
}

.table thead tr {
  font-size: 0.75rem;
  color: #374151;
  background-color: #f9fafb;
}

.dark .table thead tr {
  color: #d1d5db;
  background-color: #1f2937;
}

.table tbody tr {
  background-color: white;
  border-bottom: 1px solid #e5e7eb;
}

.dark .table tbody tr {
  background-color: #111827;
  border-bottom: 1px solid #374151;
}

.table tbody tr:hover {
  background-color: #f9fafb;
}

.dark .table tbody tr:hover {
  background-color: #1f2937;
}

.table th,
.table td {
  padding: 1rem 1.5rem;
}

.table th {
  font-weight: 500;
  color: #111827;
}

.dark .table th {
  color: white;
}

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

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.bg-success\/10 {
  background-color: #dcfce7;
}

.dark .bg-success\/10 {
  background-color: rgba(34, 197, 94, 0.2);
}

.text-success {
  color: #166534;
}

.dark .text-success {
  color: #bbf7d0;
}

.bg-danger\/10 {
  background-color: #fee2e2;
}

.dark .bg-danger\/10 {
  background-color: rgba(239, 68, 68, 0.2);
}

.text-danger {
  color: #991b1b;
}

.dark .text-danger {
  color: #fecaca;
}

.bg-warning\/10 {
  background-color: #fef3c7;
}

.dark .bg-warning\/10 {
  background-color: rgba(245, 158, 11, 0.2);
}

.text-warning {
  color: #92400e;
}

.dark .text-warning {
  color: #fde68a;
}

.bg-primary\/10 {
  background-color: #dbeafe;
}

.dark .bg-primary\/10 {
  background-color: rgba(59, 130, 246, 0.2);
}

.text-primary {
  color: #1e40af;
}

.dark .text-primary {
  color: #bfdbfe;
}

.progress {
  width: 100%;
  background-color: #e5e7eb;
  border-radius: 9999px;
  height: 0.375rem;
}

.dark .progress {
  background-color: #374151;
}

.progress-xs {
  height: 0.25rem;
}

.progress-bar {
  background-color: #2563eb;
  height: 100%;
  border-radius: 9999px;
  transition: all 0.3s ease;
}

.avatar-list-stacked {
  display: flex;
  margin-left: -0.5rem;
}

.avatar-list-stacked .avatar {
  border: 2px solid white;
}

.dark .avatar-list-stacked .avatar {
  border-color: #1f2937;
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border: 1px solid transparent;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.btn-outline-primary {
  border-color: #93c5fd;
  color: #1d4ed8;
  background-color: #eff6ff;
}

.btn-outline-primary:hover {
  background-color: #dbeafe;
}

.dark .btn-outline-primary {
  border-color: #2563eb;
  color: #60a5fa;
  background-color: rgba(30, 58, 138, 0.2);
}

.dark .btn-outline-primary:hover {
  background-color: rgba(30, 58, 138, 0.3);
}

.btn-primary {
  background-color: #2563eb;
  color: white;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-danger {
  background-color: #dc2626;
  color: white;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

.btn-success {
  background-color: #16a34a;
  color: white;
}

.btn-success:hover {
  background-color: #15803d;
}

.border-defaultborder {
  border-color: #e5e7eb;
}

.dark .border-defaultborder {
  border-color: #374151;
}

.text-\[\#8c9097\] {
  color: #8c9097;
}

.dark .text-white\/50 {
  color: rgba(255, 255, 255, 0.5);
}
</style>
