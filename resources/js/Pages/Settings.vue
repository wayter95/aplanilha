<template>
  <AppLayout :user="user">
    <div class="flex">
      <!-- Menu de Navegação Lateral -->
      <div class="w-64 bg-defaultbackground dark:bg-bodybg border-r border-defaultborder dark:border-gray-800 p-6">
        <nav class="space-y-2">
          <h3 class="text-sm font-semibold text-defaulttextcolor dark:text-defaulttextcolor uppercase tracking-wider mb-4">
            Configurações
          </h3>
          
          <a 
            href="#" 
            @click.prevent="activeSection = 'personal'"
            :class="[
              'flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              activeSection === 'personal' 
                ? 'bg-primary text-white' 
                : 'text-defaulttextcolor dark:text-defaulttextcolor hover:bg-light dark:hover:bg-bodybg'
            ]"
          >
            <div class="flex items-center">
              <i class="ri-user-line mr-3"></i>
              Dados pessoais
            </div>
            <i class="ri-arrow-right-s-line"></i>
          </a>

          <a 
            href="#" 
            @click.prevent="activeSection = 'company'"
            :class="[
              'flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              activeSection === 'company' 
                ? 'bg-primary text-white' 
                : 'text-defaulttextcolor dark:text-defaulttextcolor hover:bg-light dark:hover:bg-bodybg'
            ]"
          >
            <div class="flex items-center">
              <i class="ri-building-line mr-3"></i>
              Dados da empresa
            </div>
            <i class="ri-arrow-right-s-line"></i>
          </a>
        </nav>
      </div>

      <!-- Conteúdo Principal -->
      <div class="flex-1 p-6">
        <!-- Cabeçalho -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Configurações
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            Gerencie as informações de conta, dados pessoais e configurações da empresa
          </p>
        </div>

        <!-- Conteúdo Dinâmico -->
        <div class="space-y-6">
          <!-- Dados Pessoais -->
          <div v-if="activeSection === 'personal'" class="space-y-6">

            <!-- Card Dados Pessoais -->
            <div class="bg-defaultbackground dark:bg-bodybg rounded-lg border border-defaultborder dark:border-gray-800 p-6">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Dados pessoais
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Informações básicas da sua conta
                  </p>
                </div>
                <button 
                  @click="showPersonalDataModal = true"
                  class="text-primary hover:text-primary-dark text-sm font-medium"
                >
                  Alterar
                </button>
              </div>
              
              <!-- Upload de Foto -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Foto do perfil
                </label>
                <PhotoUpload 
                  v-model="userPhoto"
                  :current-photo-url="currentPhotoUrl"
                  alt-text="Foto do usuário"
                  folder="users/photos"
                  @upload-success="handlePhotoUploadSuccess"
                  @upload-error="handlePhotoUploadError"
                />
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-user-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nome</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.name || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-at-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Usuário</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.username || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-mail-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">E-mail</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.email || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-phone-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Telefone</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.phone || 'Não informado' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Dados de Acesso -->
            <div class="bg-defaultbackground dark:bg-bodybg rounded-lg border border-defaultborder dark:border-gray-800 p-6">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Dados de acesso
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Credenciais para entrar na plataforma
                  </p>
                </div>
                <button 
                  @click="showPasswordModal = true"
                  class="text-primary hover:text-primary-dark text-sm font-medium"
                >
                  Alterar
                </button>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-mail-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">E-mail</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.email || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-at-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Usuário</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ props.user?.username || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-light dark:bg-bodybg rounded-lg flex items-center justify-center">
                    <i class="ri-lock-line text-defaulttextcolor dark:text-defaulttextcolor"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Senha</p>
                    <p class="font-medium text-gray-900 dark:text-white">*********</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dados da Empresa -->
          <div v-if="activeSection === 'company'" class="space-y-6">
            <!-- Card Dados da Empresa -->
            <div class="bg-defaultbackground dark:bg-bodybg rounded-lg border border-defaultborder dark:border-gray-800 p-6">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Dados da empresa
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Informações da sua organização
                  </p>
                </div>
                <button class="text-primary hover:text-primary-dark text-sm font-medium">
                  Alterar
                </button>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="ri-building-line text-gray-600 dark:text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nome da empresa</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ company.name || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="ri-file-text-line text-gray-600 dark:text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">CNPJ</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ company.cnpj || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="ri-mail-line text-gray-600 dark:text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">E-mail comercial</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ company.email || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="ri-phone-line text-gray-600 dark:text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Telefone comercial</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ company.phone || 'Não informado' }}</p>
                  </div>
                </div>

                <div class="flex items-center space-x-3 md:col-span-2">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="ri-map-pin-line text-gray-600 dark:text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Endereço</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ company.address || 'Não informado' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Modais -->
  <UpdatePersonalDataModal 
    :show="showPersonalDataModal" 
    :user="props.user"
    @close="showPersonalDataModal = false"
    @personal-data-updated="handlePersonalDataUpdated"
  />
  
  <UpdatePasswordModal 
    :show="showPasswordModal" 
    @close="showPasswordModal = false"
    @password-updated="handlePasswordUpdated"
  />
</template>

<script setup>
import PhotoUpload from '@/Components/PhotoUpload.vue'
import UpdatePasswordModal from '@/Components/SettingsModals/UpdatePasswordModal.vue'
import UpdatePersonalDataModal from '@/Components/SettingsModals/UpdatePersonalDataModal.vue'
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { useToast } from '@/composables/useToast'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref } from 'vue'

const { success, error: showError } = useToast()
const { getPhotoUrl } = usePhotoUrl()
const page = usePage()

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  }
})

const activeSection = ref('personal')
const userPhoto = ref(props.user?.photo_key || null) // Mudança: usar photo_key em vez de avatar_path
const currentPhotoUrl = ref(null)

// Estados dos modais
const showPersonalDataModal = ref(false)
const showPasswordModal = ref(false)

// Função para carregar a URL temporária da foto
const loadUserPhotoUrl = async () => {
  if (userPhoto.value) {
    // Se é uma key, buscar URL temporária usando o composable
    return await getPhotoUrl(userPhoto.value)
  }
  return null
}

// Função para atualizar a foto localmente
const updateLocalPhoto = async (photoKey, photoUrl) => {
  console.log('Settings: Atualizando foto localmente', { photoKey, photoUrl })
  userPhoto.value = photoKey // Mudança: usar key em vez de URL completa
  currentPhotoUrl.value = photoUrl
  
  // Emitir evento global para atualizar outros componentes
  console.log('Settings: Emitindo evento global user-photo-updated')
  window.dispatchEvent(new CustomEvent('user-photo-updated', {
    detail: {
      userId: props.user.id,
      photoKey: photoKey, // Mudança: enviar photoKey em vez de avatarPath
      photoUrl: photoUrl
    }
  }))
}

// Dados da empresa (mockados por enquanto)
const company = ref({
  name: 'Empresa Demo Ltda',
  cnpj: '12.345.678/0001-90',
  email: 'contato@empresademo.com',
  phone: '+55 34 99930-5492',
  address: 'Rua das Flores, 123 - Centro, Uberlândia - MG'
})

// Funções para upload de foto
const handlePhotoUploadSuccess = async (data) => {
  console.log('Settings: Upload bem-sucedido', data)
  if (data) {
    // Atualizar o usuário no backend usando fetch com CSRF token do Inertia
    try {
      console.log('Settings: Enviando requisição para atualizar foto no backend')
      
      const response = await fetch(`/api/users/${props.user.id}/photo`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': page.props.csrf_token,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          photo_key: data.key // Mudança: enviar photo_key em vez de avatar_path
        })
      })

      console.log('Settings: Resposta do backend', response.status)
      if (response.ok) {
        const result = await response.json()
        console.log('Settings: Resultado do backend', result)
        if (result.success) {
          // Atualizar localmente usando a nova função
          updateLocalPhoto(data.key, data.url) // Mudança: usar data.url em vez de data.awsUrl
          success('Foto atualizada com sucesso!')
        } else {
          showError('Erro ao atualizar foto: ' + result.message)
        }
      } else {
        const errorResult = await response.json()
        console.error('Settings: Erro na resposta', errorResult)
        showError('Erro ao atualizar foto: ' + (errorResult.message || 'Erro desconhecido'))
      }
    } catch (error) {
      console.error('Erro ao atualizar foto:', error)
      showError('Erro ao atualizar foto no sistema')
    }
  }
}

const handlePhotoUploadError = (error) => {
  console.error('Erro no upload:', error)
}

// Escutar eventos de atualização da foto para sincronizar com outros componentes
const handlePhotoUpdate = async (event) => {
  if (event.detail.userId === props.user?.id) {
    await updateLocalPhoto(event.detail.avatarPath, event.detail.photoUrl)
  }
}

onMounted(async () => {
  console.log('Settings: Usuário carregado:', props.user)
  console.log('Settings: Photo key:', props.user?.photo_key)
  
  // Carregar a URL da foto atual se existir
  if (userPhoto.value) {
    console.log('Settings: Carregando foto existente:', userPhoto.value)
    // Sempre usar o composable para gerar URL temporária
    currentPhotoUrl.value = await loadUserPhotoUrl()
  } else {
    console.log('Settings: Nenhuma foto encontrada')
  }
  
  // Escutar eventos globais de atualização da foto
  window.addEventListener('user-photo-updated', handlePhotoUpdate)
})

onUnmounted(() => {
  window.removeEventListener('user-photo-updated', handlePhotoUpdate)
})

// Funções de callback para os modais
const handlePersonalDataUpdated = (updatedUser) => {
  // Atualizar os dados do usuário na página
  if (updatedUser) {
    // Recarregar a página para atualizar os dados
    router.reload()
  }
}

const handlePasswordUpdated = () => {
  // Senha foi alterada com sucesso
  // Não precisa fazer nada específico além do toast que já é exibido no modal
}

</script>
