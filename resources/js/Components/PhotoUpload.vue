<template>
  <div class="photo-upload-container">
    <!-- Avatar com botão de alterar -->
    <div class="flex items-center space-x-4">
      <!-- Avatar -->
      <div class="relative">
        <div class="w-20 h-20 overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center" style="border-radius: 50%;">
          <img 
            v-if="currentPhotoUrl" 
            :src="currentPhotoUrl" 
            :alt="altText"
            class="w-full h-full object-cover"
          />
          <i v-else class="ri-user-line text-3xl text-gray-400 dark:text-gray-500"></i>
        </div>
        
        <!-- Botão de alterar -->
        <button
          @click="openModal"
          class="absolute -bottom-1 -right-1 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary-dark transition-colors shadow-lg"
          title="Alterar foto"
        >
          <i class="ri-camera-line text-sm"></i>
        </button>
      </div>
      
      <!-- Informações -->
      <div>
        <h4 class="font-medium text-gray-900 dark:text-white">{{ altText }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ currentPhotoUrl ? 'Foto atualizada' : 'Nenhuma foto' }}
        </p>
      </div>
    </div>

    <!-- Modal de Upload -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              {{ currentPhotoUrl ? 'Alterar Foto' : 'Adicionar Foto' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <i class="ri-close-line text-xl"></i>
            </button>
          </div>

          <!-- Área de upload -->
          <div class="space-y-4">
            <input
              ref="fileInput"
              type="file"
              accept="image/*"
              @change="handleFileSelect"
              class="hidden"
            />
            
            <div
              @click="triggerFileInput"
              @dragover.prevent
              @drop.prevent="handleFileDrop"
              :class="[
                'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors',
                isDragOver 
                  ? 'border-primary bg-primary/5' 
                  : 'border-gray-300 dark:border-gray-600 hover:border-primary hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
            >
              <div v-if="!isUploading" class="space-y-3">
                <i class="ri-upload-cloud-2-line text-4xl text-gray-400 dark:text-gray-500"></i>
                <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    {{ isDragOver ? 'Solte a imagem aqui' : 'Clique para selecionar ou arraste uma imagem' }}
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

            <!-- Preview da nova foto -->
            <div v-if="previewUrl" class="text-center">
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Preview:</p>
              <img 
                :src="previewUrl" 
                alt="Preview"
                class="w-24 h-24 rounded-full object-cover mx-auto border-2 border-gray-200 dark:border-gray-600"
              />
            </div>

            <!-- Mensagens de erro -->
            <div v-if="error" class="text-sm text-red-600 dark:text-red-400">
              {{ error }}
            </div>
          </div>

          <!-- Botões do modal -->
          <div class="flex justify-end gap-2 mt-6">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
            >
              Cancelar
            </button>
            <button 
              v-if="previewUrl && !isUploading"
              type="button" 
              @click="confirmUpload" 
              class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors"
            >
              Confirmar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const { success, error: showError } = useToast()
const { getPhotoUrl } = usePhotoUrl()
const page = usePage()

const props = defineProps({
  modelValue: {
    type: String,
    default: null
  },
  currentPhotoUrl: {
    type: String,
    default: null
  },
  altText: {
    type: String,
    default: 'Foto do usuário'
  },
  folder: {
    type: String,
    default: 'users/photos'
  }
})

const emit = defineEmits(['update:modelValue', 'upload-success', 'upload-error', 'photo-updated'])

const fileInput = ref(null)
const showModal = ref(false)
const isUploading = ref(false)
const isDragOver = ref(false)
const error = ref('')
const currentPhotoUrl = ref(props.currentPhotoUrl || props.modelValue)
const previewUrl = ref('')
const selectedFile = ref(null)

// Watch para atualizar a URL quando o modelValue ou currentPhotoUrl mudar
watch(() => [props.modelValue, props.currentPhotoUrl], ([newModelValue, newCurrentPhotoUrl]) => {
  // Se é uma URL completa, usar diretamente
  if (newCurrentPhotoUrl && newCurrentPhotoUrl.startsWith('http')) {
    currentPhotoUrl.value = newCurrentPhotoUrl
  } else if (newModelValue && newModelValue.startsWith('http')) {
    currentPhotoUrl.value = newModelValue
  } else {
    currentPhotoUrl.value = newCurrentPhotoUrl || newModelValue
  }
}, { immediate: true })

const openModal = () => {
  showModal.value = true
  error.value = ''
  previewUrl.value = ''
  selectedFile.value = null
}

const closeModal = () => {
  showModal.value = false
  error.value = ''
  previewUrl.value = ''
  selectedFile.value = null
  isDragOver.value = false
}

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

    // 3. Gerar URL temporária para visualização
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

    // 4. Atualizar o componente
    currentPhotoUrl.value = tempUrlData.url // Mudança: usar 'url' em vez de 'temporary_url'
    
    // 5. Emitir eventos com a key (não a URL completa)
    emit('update:modelValue', presignedData.key) // Mudança: enviar apenas a key
    emit('upload-success', {
      key: presignedData.key,
      url: tempUrlData.url, // URL temporária para preview
      filename: selectedFile.value.name
    })
    emit('photo-updated', {
      key: presignedData.key,
      url: tempUrlData.url, // URL temporária para preview
      filename: selectedFile.value.name
    })

    success('Foto atualizada com sucesso!')
    closeModal()

  } catch (err) {
    console.error('Erro no upload:', err)
    error.value = err.message || 'Erro ao fazer upload da foto'
    emit('upload-error', err)
    showError(error.value)
  } finally {
    isUploading.value = false
  }
}

// Event listeners para drag and drop
const handleDragOver = (event) => {
  event.preventDefault()
  isDragOver.value = true
}

const handleDragLeave = () => {
  isDragOver.value = false
}
</script>

<style scoped>
.photo-upload-container {
  transition: all 0.2s ease;
}
</style>
