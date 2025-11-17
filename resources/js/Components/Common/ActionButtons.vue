<template>
  <div class="flex items-center gap-2">
    <!-- Botão Editar -->
    <button
      v-if="showEdit"
      type="button"
      class="ti-btn ti-btn-sm ti-btn-icon ti-btn-primary-full"
      @click="emit('edit')"
      :disabled="disabled"
      :title="editLabel"
    >
      <i class="ri-pencil-line"></i>
    </button>

    <!-- Botão Visualizar -->
    <button
      v-if="showView"
      type="button"
      class="ti-btn ti-btn-sm ti-btn-icon ti-btn-info-full"
      @click="emit('view')"
      :disabled="disabled"
      :title="viewLabel"
    >
      <i class="ri-eye-line"></i>
    </button>

    <!-- Botão Deletar -->
    <button
      v-if="showDelete"
      type="button"
      class="ti-btn ti-btn-sm ti-btn-icon ti-btn-danger-full"
      @click="emit('delete')"
      :disabled="disabled"
      :title="deleteLabel"
    >
      <i class="ri-delete-bin-line"></i>
    </button>

    <!-- Botão Ativar/Desativar -->
    <button
      v-if="showToggleStatus"
      type="button"
      :class="[
        'ti-btn ti-btn-sm ti-btn-icon',
        isActive ? 'ti-btn-warning-full' : 'ti-btn-success-full'
      ]"
      @click="emit('toggle-status')"
      :disabled="disabled"
      :title="isActive ? blockLabel : activateLabel"
    >
      <i :class="isActive ? 'ri-close-circle-line' : 'ri-check-circle-line'"></i>
    </button>

    <!-- Botão Download -->
    <button
      v-if="showDownload"
      type="button"
      class="ti-btn ti-btn-sm ti-btn-icon ti-btn-secondary-full"
      @click="emit('download')"
      :disabled="disabled"
      :title="downloadLabel"
    >
      <i class="ri-download-line"></i>
    </button>

    <!-- Botão Duplicar -->
    <button
      v-if="showDuplicate"
      type="button"
      class="ti-btn ti-btn-sm ti-btn-icon ti-btn-secondary-full"
      @click="emit('duplicate')"
      :disabled="disabled"
      :title="duplicateLabel"
    >
      <i class="ri-file-copy-line"></i>
    </button>

    <!-- Botões personalizados -->
    <button
      v-for="(action, index) in customActions"
      :key="index"
      type="button"
      :class="[
        'ti-btn ti-btn-sm ti-btn-icon',
        action.variant ? `ti-btn-${action.variant}-full` : 'ti-btn-secondary-full'
      ]"
      @click="emit('custom-action', action.name)"
      :disabled="disabled || action.disabled"
      :title="action.label"
    >
      <i :class="action.icon"></i>
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  // Controle de visibilidade dos botões
  showEdit: {
    type: Boolean,
    default: true
  },
  showView: {
    type: Boolean,
    default: false
  },
  showDelete: {
    type: Boolean,
    default: true
  },
  showToggleStatus: {
    type: Boolean,
    default: false
  },
  showDownload: {
    type: Boolean,
    default: false
  },
  showDuplicate: {
    type: Boolean,
    default: false
  },
  
  // Estado
  isActive: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  
  // Labels customizados
  editLabel: {
    type: String,
    default: 'Editar'
  },
  viewLabel: {
    type: String,
    default: 'Visualizar'
  },
  deleteLabel: {
    type: String,
    default: 'Excluir'
  },
  activateLabel: {
    type: String,
    default: 'Ativar'
  },
  blockLabel: {
    type: String,
    default: 'Bloquear'
  },
  downloadLabel: {
    type: String,
    default: 'Download'
  },
  duplicateLabel: {
    type: String,
    default: 'Duplicar'
  },
  
  // Ações customizadas
  customActions: {
    type: Array,
    default: () => []
    // Formato: [{ name: 'action-name', icon: 'ri-icon-name', label: 'Label', variant: 'primary', disabled: false }]
  }
})

const emit = defineEmits([
  'edit',
  'view',
  'delete',
  'toggle-status',
  'download',
  'duplicate',
  'custom-action'
])
</script>
