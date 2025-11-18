<template>
  <Modal
    :show="show"
    title="Excluir Tipo de Projeto"
    description="Esta ação não pode ser desfeita."
    icon="ri-delete-bin-line"
    type="danger"
    size="md"
    @close="closeModal"
  >
    <!-- Informações do Tipo de Projeto -->
    <div v-if="type" class="bg-primary/5 dark:bg-primary/10 border border-primary/20 dark:border-primary/30 rounded-md p-4 mb-4">
      <div class="flex items-start gap-3">
        <!-- Ícone colorido -->
        <div 
          class="flex-shrink-0 w-10 h-10 rounded-md flex items-center justify-center shadow-sm"
          :style="{ backgroundColor: type.color || '#6c757d' }"
        >
          <i class="ri-folder-line text-white text-lg"></i>
        </div>
        
        <!-- Informações -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1.5">
            <h4 class="text-sm font-semibold text-defaulttextcolor dark:text-white truncate">
              {{ type.title }}
            </h4>
            <Badge 
              :variant="type.status === 'a' ? 'primary' : 'secondary'"
              :label="type.status === 'a' ? 'Ativo' : 'Bloqueado'"
              size="xs"
              soft
            />
          </div>
          <div class="flex items-center gap-1.5 text-xs text-textmuted dark:text-white/50">
            <i class="ri-palette-line text-sm text-primary"></i>
            <span class="font-mono">{{ type.color }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mensagem de Confirmação -->
    <div class="space-y-3">
      <p class="text-sm text-textmuted dark:text-white/70">
        O tipo de projeto <strong class="text-defaulttextcolor dark:text-white">{{ type?.title }}</strong> será permanentemente excluído do sistema.
      </p>
      
      <div class="bg-danger/10 border border-danger/20 rounded-md p-3">
        <div class="flex items-start gap-2">
          <i class="ri-error-warning-line text-danger text-base mt-0.5 flex-shrink-0"></i>
          <p class="text-xs text-danger">
            <strong>Atenção:</strong> Esta ação não pode ser revertida. Todos os dados relacionados serão perdidos.
          </p>
        </div>
      </div>
    </div>

    <!-- Footer com Botões -->
    <template #footer>
      <div class="flex items-center justify-end gap-2 w-full">
        <Button
          variant="light"
          style-type="outline"
          label="Cancelar"
          size="sm"
          @click="closeModal"
          :disabled="processing"
        />
        <Button
          variant="danger"
          style-type="solid"
          size="sm"
          :loading="processing"
          @click="deleteType"
        >
          <i class="ri-delete-bin-line me-1"></i>
          Excluir
        </Button>
      </div>
    </template>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'
import Badge from '@/Components/Common/Badge.vue'
import { useToast } from '@/composables/useToast'
import { ref } from 'vue'
import projectTypesService from '@/api/projectTypesService'

const toast = useToast()

const props = defineProps({
  show: Boolean,
  type: Object,
})

const emit = defineEmits(['close', 'type-deleted'])

const processing = ref(false)

const deleteType = async () => {
  if (!props.type?.id) {
    toast.error('ID do tipo de projeto não encontrado')
    return
  }

  processing.value = true
  
  try {
    const data = await projectTypesService.delete(props.type.id)
    
    if (data.success) {
      toast.success(data.message || 'Tipo de projeto excluído com sucesso!')
      emit('type-deleted')
      closeModal()
    } else {
      toast.error(data.message || 'Erro ao excluir tipo de projeto')
    }
  } catch (error) {
    console.error('Erro ao excluir tipo de projeto:', error)
    toast.error(error.response?.data?.message || 'Erro ao excluir tipo de projeto')
  } finally {
    processing.value = false
  }
}

const closeModal = () => {
  emit('close')
}
</script>
