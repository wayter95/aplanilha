<!--
    🖼️ ImageUpload - Componente para upload de imagens com assinatura de URL
    Baseado no PhotoUpload mas adaptado para uso geral (marca d'água, etc)
-->

<template>
  <div class="image-upload-container">
    <!-- Preview da imagem atual -->
    <div v-if="currentImageUrl" class="mb-4">
      <div class="relative inline-block">
        <img 
          :src="currentImageUrl" 
          :alt="altText"
          class="max-w-full max-h-48 rounded-lg border-2 border-gray-200 dark:border-gray-700 object-contain"
        />
        <button
          @click="removeImage"
          class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg text-xs"
          title="Remover imagem"
        >
          ✕
        </button>
      </div>
    </div>

    <!-- Área de upload -->
    <div
      @click="triggerFileInput"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave"
      @drop.prevent="handleFileDrop"
      :class="[
        'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all duration-200',
        isDragOver 
          ? 'border-primary bg-primary/5 dark:bg-primary/10' 
          : 'border-gray-300 dark:border-gray-600 hover:border-primary hover:bg-gray-50 dark:hover:bg-gray-700/50',
        isUploading ? 'opacity-50 cursor-not-allowed' : ''
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <div v-if="!isUploading" class="space-y-3">
        <i class="ri-image-add-line text-4xl text-gray-400 dark:text-gray-500"></i>
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
            {{ isDragOver ? 'Solte a imagem aqui' : (currentImageUrl ? 'Clique para trocar a imagem' : 'Clique para selecionar ou arraste uma imagem') }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
            PNG, JPG, JPEG até 5MB
          </p>
        </div>
      </div>
      
      <div v-else class="space-y-3">
        <div class="inline-block animate-spin">
          <i class="ri-loader-4-line text-4xl text-primary"></i>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Enviando...
        </p>
      </div>
    </div>

    <!-- Preview da nova imagem selecionada -->
    <div v-if="previewUrl && !isUploading" class="mt-4 text-center">
      <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Preview:</p>
      <img 
        :src="previewUrl" 
        alt="Preview"
        class="max-w-full max-h-48 rounded-lg border-2 border-gray-200 dark:border-gray-700 object-contain mx-auto"
      />
      <div class="flex justify-center gap-2 mt-3">
        <button 
          type="button" 
          @click="confirmUpload" 
          class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors"
        >
          Confirmar upload
        </button>
        <button 
          type="button" 
          @click="cancelPreview" 
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
        >
          Cancelar
        </button>
      </div>
    </div>

    <!-- Mensagens de erro -->
    <div v-if="error" class="mt-3 text-sm text-red-600 dark:text-red-400">
      <i class="ri-error-warning-line me-1"></i>
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

const { success, error: showError } = useToast()
const { getPhotoUrl } = usePhotoUrl()
const page = usePage()

const props = defineProps({
  modelValue: {
    type: String,
    default: null
  },
  currentImageUrl: {
    type: String,
    default: null
  },
  altText: {
    type: String,
    default: 'Imagem'
  },
  folder: {
    type: String,
    default: 'document-templates/watermarks'
  }
})

const emit = defineEmits(['update:modelValue', 'upload-success', 'upload-error', 'image-updated'])

const fileInput = ref(null)
const isUploading = ref(false)
const isDragOver = ref(false)
const error = ref('')
const displayImageUrl = ref(null)
const previewUrl = ref('')
const selectedFile = ref(null)

// Função para carregar URL da imagem
const loadImageUrl = async () => {
  if (props.currentImageUrl && props.currentImageUrl.startsWith('http')) {
    displayImageUrl.value = props.currentImageUrl
    return
  }
  if (props.modelValue && props.modelValue.startsWith('http')) {
    displayImageUrl.value = props.modelValue
    return
  }
  if (props.modelValue && !props.modelValue.startsWith('http')) {
    const url = await getPhotoUrl(props.modelValue)
    displayImageUrl.value = url || props.currentImageUrl || props.modelValue
  } else {
    displayImageUrl.value = props.currentImageUrl || props.modelValue
  }
}

// Computed para URL da imagem atual (sincronizado)
const currentImageUrl = computed(() => displayImageUrl.value)

// Watch para atualizar a URL quando o modelValue ou currentImageUrl mudar
watch(() => [props.modelValue, props.currentImageUrl], async () => {
  await loadImageUrl()
}, { immediate: true })

// Carregar URL na inicialização
loadImageUrl()

const triggerFileInput = () => {
  if (!isUploading.value) {
    fileInput.value?.click()
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    validateAndPreviewFile(file)
  }
}

const handleFileDrop = (event) => {
  isDragOver.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    validateAndPreviewFile(file)
  }
}

const handleDragOver = () => {
  isDragOver.value = true
}

const handleDragLeave = () => {
  isDragOver.value = false
}

const validateAndPreviewFile = (file) => {
  // Validações
  if (!file.type.startsWith('image/')) {
    error.value = 'Por favor, selecione apenas arquivos de imagem.'
    return
  }

  if (file.size > 5 * 1024 * 1024) { // 5MB
    error.value = 'O arquivo deve ter no máximo 5MB.'
    return
  }

  error.value = ''
  selectedFile.value = file
  
  // Criar preview
  const reader = new FileReader()
  reader.onload = (e) => {
    previewUrl.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const cancelPreview = () => {
  previewUrl.value = ''
  selectedFile.value = null
  error.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const removeImage = () => {
  if (confirm('Deseja remover esta imagem?')) {
    displayImageUrl.value = null
    emit('update:modelValue', null)
    emit('image-updated', { key: null, url: null })
  }
}

const confirmUpload = async () => {
  if (!selectedFile.value) return

  error.value = ''
  isUploading.value = true

  try {
    // 1. Solicitar URL pré-assinada
    const presignedResponse = await fetch('/api/files/presigned-url', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify({
        filename: selectedFile.value.name,
        content_type: selectedFile.value.type,
        folder: props.folder,
        auto_generate_name: true
      })
    })

    const presignedData = await presignedResponse.json()

    if (!presignedData.success) {
      throw new Error(presignedData.message || 'Erro ao gerar URL pré-assinada')
    }

    // 2. Fazer upload direto para S3
    const uploadResponse = await fetch(presignedData.url, {
      method: 'PUT',
      headers: {
        'Content-Type': selectedFile.value.type,
      },
      body: selectedFile.value
    })

    if (!uploadResponse.ok) {
      throw new Error('Erro no upload para S3')
    }

    // 3. Obter URL assinada para exibição
    const tempUrlResponse = await fetch(`/api/files/signed-url?key=${presignedData.key}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': page.props.csrf_token
      }
    })

    const tempUrlData = await tempUrlResponse.json()

    if (!tempUrlData.success) {
      throw new Error(tempUrlData.message || 'Erro ao gerar URL temporária')
    }

    displayImageUrl.value = tempUrlData.url
    
    emit('update:modelValue', presignedData.key)
    emit('upload-success', {
      key: presignedData.key,
      url: tempUrlData.url,
      filename: selectedFile.value.name
    })
    emit('image-updated', {
      key: presignedData.key,
      url: tempUrlData.url,
      filename: selectedFile.value.name
    })

    success('Imagem enviada com sucesso!')
    cancelPreview()

  } catch (err) {
    console.error('Erro no upload:', err)
    error.value = err.message || 'Erro ao fazer upload da imagem'
    emit('upload-error', err)
    showError(error.value)
  } finally {
    isUploading.value = false
  }
}
</script>

<style scoped>
.image-upload-container {
  transition: all 0.2s ease;
}
</style>

