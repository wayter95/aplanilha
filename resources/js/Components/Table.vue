<template>
  <div class="table-responsive">
    <table class="table whitespace-nowrap table-hover min-w-full !text-[0.75rem]">
      <thead>
        <tr class="border-b border-defaultborder">
          <th 
            v-for="column in columns" 
            :key="column.key"
            scope="col" 
            class="text-start !py-2 !px-3 !text-[0.75rem] !font-medium"
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
          <td v-for="column in columns" :key="column.key" class="!py-2 !px-3">
            <slot 
              :name="`cell-${column.key}`" 
              :row="row" 
              :value="getNestedValue(row, column.key)"
              :column="column"
              :rowIndex="rowIndex"
            >
              <div v-if="column.type === 'avatar'">
                <div class="flex items-center gap-2">
                  <span class="avatar avatar-xs avatar-rounded">
                    <img 
                      v-if="getNestedValue(row, column.imageKey)"
                      :src="getNestedValue(row, column.imageKey)" 
                      :alt="getNestedValue(row, column.nameKey)"
                    >
                    <div 
                      v-else
                      class="w-7 h-7 bg-primary/10 text-primary rounded-full flex items-center justify-center !text-[0.65rem] font-semibold"
                    >
                      {{ getInitials(getNestedValue(row, column.nameKey)) }}
                    </div>
                  </span>
                  <div v-if="column.showInfo" class="leading-tight">
                    <div class="leading-none mb-0.5">
                      <span class="!text-[0.75rem]">{{ getNestedValue(row, column.nameKey) }}</span>
                    </div>
                    <div class="leading-none" v-if="column.emailKey">
                      <span class="!text-[0.65rem] text-[#8c9097] dark:text-white/50">
                        {{ getNestedValue(row, column.emailKey) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else-if="column.type === 'badge'">
                <span 
                  :class="[
                    'badge !text-[0.65rem] !py-0.5 !px-2',
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
                    class="avatar avatar-xs avatar-rounded"
                  >
                    <img v-if="avatar.image" :src="avatar.image" :alt="avatar.name">
                    <div v-else class="w-7 h-7 bg-light text-defaulttextcolor rounded-full flex items-center justify-center !text-[0.65rem] font-semibold">
                      {{ getInitials(avatar.name) }}
                    </div>
                  </span>
                  <a 
                    v-if="getNestedValue(row, column.remainingKey)"
                    class="avatar avatar-xs bg-primary text-white avatar-rounded !text-[0.65rem]"
                    href="javascript:void(0);"
                  >
                    +{{ getNestedValue(row, column.remainingKey) }}
                  </a>
                </div>
              </div>

              <div v-else-if="column.type === 'actions'">
                <div class="hstack gap-2 flex-wrap">
                  <button
                    v-for="action in column.actions"
                    :key="action.name"
                    @click="handleAction(action.name, row, rowIndex)"
                    :class="[
                      'ti-btn ti-btn-sm',
                      action.class || 'ti-btn-outline-primary'
                    ]"
                    :title="action.title"
                  >
                    <i :class="action.icon" v-if="action.icon"></i>
                    <span v-if="action.label" class="!text-[0.65rem]">{{ action.label }}</span>
                  </button>
                </div>
              </div>

              <span v-else class="!text-[0.75rem]">{{ getNestedValue(row, column.key) }}</span>
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!data || data.length === 0" class="text-center py-8">
      <div class="text-[#8c9097] dark:text-white/50">
        <i class="ri-table-line !text-4xl mb-2"></i>
        <p class="!text-[0.813rem]">{{ emptyMessage || 'Nenhum registro encontrado' }}</p>
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

  // Classes padrão do template Ynex
  const defaultClasses = {
    'Ativo': 'bg-success/10 text-success',
    'Inativo': 'bg-danger/10 text-danger',
    'Bloqueado': 'bg-danger/10 text-danger',
    'Pendente': 'bg-warning/10 text-warning',
    'Concluído': 'bg-primary/10 text-primary',
    'Active': 'bg-success/10 text-success',
    'Inactive': 'bg-danger/10 text-danger'
  }

  return defaultClasses[value] || 'bg-light text-dark'
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
/* Usa as classes do template Ynex - sem customizações CSS */
/* Avatar list stacked spacing */
.avatar-list-stacked {
  display: flex;
  margin-left: -0.5rem;
}

.avatar-list-stacked .avatar {
  border: 2px solid white;
  margin-left: -0.5rem;
}

.dark .avatar-list-stacked .avatar {
  border-color: rgb(var(--body-bg));
}

/* Progress bar */
.progress-xs {
  height: 0.25rem;
}
</style>
