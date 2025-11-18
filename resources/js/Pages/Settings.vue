<template>
  <AppLayout title="Configurações" description="Gerencie suas configurações de conta e empresa">
    <!-- Breadcrumb -->
    <Breadcrumb
      title="Configurações"
      :items="breadcrumbItems"
    />
    
    <div class="grid grid-cols-12 gap-6">
      <!-- Menu lateral de navegação -->
      <div class="xl:col-span-3 col-span-12">
        <div class="box">
          <div class="box-header">
            <div class="box-title">
              Menu de Configurações
            </div>
          </div>
          <div class="box-body p-0">
            <nav class="space-y-1">
              <a 
                href="#" 
                @click.prevent="activeSection = 'personal'"
                :class="[
                  'flex items-center px-4 py-3 text-sm font-medium transition-colors border-b border-defaultborder dark:border-gray-700',
                  activeSection === 'personal' 
                    ? 'bg-primary/10 text-primary border-r-2 border-r-primary' 
                    : 'text-defaulttextcolor dark:text-defaulttextcolor hover:bg-light dark:hover:bg-gray-700'
                ]"
              >
                <i class="ri-user-line mr-3 text-base"></i>
                <div>
                  <div class="font-medium">Dados Pessoais</div>
                  <div class="text-xs opacity-75">Informações do usuário</div>
                </div>
              </a>

              <a 
                href="#" 
                @click.prevent="activeSection = 'company'"
                :class="[
                  'flex items-center px-4 py-3 text-sm font-medium transition-colors',
                  activeSection === 'company' 
                    ? 'bg-primary/10 text-primary border-r-2 border-r-primary' 
                    : 'text-defaulttextcolor dark:text-defaulttextcolor hover:bg-light dark:hover:bg-gray-700'
                ]"
              >
                <i class="ri-building-line mr-3 text-base"></i>
                <div>
                  <div class="font-medium">Dados da Empresa</div>
                  <div class="text-xs opacity-75">Informações corporativas</div>
                </div>
              </a>
            </nav>
          </div>
        </div>
      </div>

      <!-- Conteúdo principal -->
      <div class="xl:col-span-9 col-span-12">
        <!-- Seção Dados Pessoais -->
        <div v-if="activeSection === 'personal'" class="space-y-6">
          
          <!-- Card Informações Pessoais -->
          <div class="box">
            <div class="box-header">
              <div class="box-title">
                Informações Pessoais
              </div>
              <Button
                variant="primary"
                style-type="outline"
                size="xs"
                left-icon="ri-edit-line"
                class="h-7 px-2 py-1 text-xs"
                @click="showPersonalDataModal = true"
              >
                Editar
              </Button>
            </div>
            <div class="box-body">
              <!-- Layout compacto: Foto à esquerda, informações à direita -->
              <div class="flex items-start space-x-6 mb-6">
                <!-- Foto do Perfil -->
                <div class="flex-shrink-0">
                  <PhotoUpload 
                    v-model="userPhoto"
                    :current-photo-url="currentPhotoUrl"
                    alt-text="Foto do usuário"
                    folder="users/photos"
                    @upload-success="handlePhotoUploadSuccess"
                    @upload-error="handlePhotoUploadError"
                  />
                </div>
                
                <!-- Informações Pessoais -->
                <div class="flex-1">
                  <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Informações Pessoais
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome Completo</label>
                      <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                        {{ user?.name || 'Não informado' }}
                      </p>
                    </div>

                    <div class="space-y-1">
                      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome de Usuário</label>
                      <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                        {{ user?.username || 'Não informado' }}
                      </p>
                    </div>

                    <div class="space-y-1">
                      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">E-mail</label>
                      <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                        {{ user?.email || 'Não informado' }}
                      </p>
                    </div>

                    <div class="space-y-1">
                      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefone</label>
                      <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                        {{ user?.phone || 'Não informado' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card Dados de Acesso -->
          <div class="box">
            <div class="box-header">
              <div class="box-title">
                Dados de Acesso
              </div>
              <Button
                variant="primary"
                style-type="outline"
                size="xs"
                left-icon="ri-lock-line"
                class="h-7 px-2 py-1 text-xs"
                @click="showPasswordModal = true"
              >
                Alterar Senha
              </Button>
            </div>
            <div class="box-body">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário de Acesso</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ user?.username || 'Não informado' }}
                  </p>
                </div>

                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Senha</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor flex items-center">
                    <span class="mr-2">••••••••••</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">(Última alteração: nunca)</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção Dados da Empresa -->
        <div v-if="activeSection === 'company'" class="space-y-6">
          <div class="box">
            <div class="box-header">
              <div class="box-title">
                Informações da Empresa
              </div>
              <Button
                variant="primary"
                style-type="outline"
                size="xs"
                left-icon="ri-edit-line"
                class="h-7 px-2 py-1 text-xs"
                @click="showCompanyDataModal = true"
              >
                Editar
              </Button>
            </div>
            <div class="box-body">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome da Empresa</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ company?.name || 'Não informado' }}
                  </p>
                </div>

                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">CNPJ</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ company?.cnpj || 'Não informado' }}
                  </p>
                </div>

                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">E-mail Comercial</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ company?.email || 'Não informado' }}
                  </p>
                </div>

                <div class="space-y-1">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefone Comercial</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ company?.phone || 'Não informado' }}
                  </p>
                </div>

                <div class="space-y-1 md:col-span-2">
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Endereço</label>
                  <p class="text-sm font-medium text-defaulttextcolor dark:text-defaulttextcolor">
                    {{ company?.address || 'Não informado' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modais -->
    <UpdatePersonalDataModal 
      :show="showPersonalDataModal" 
      :user="user"
      @close="showPersonalDataModal = false"
      @personal-data-updated="handlePersonalDataUpdated"
    />
    
    <UpdatePasswordModal 
      :show="showPasswordModal" 
      @close="showPasswordModal = false"
      @password-updated="handlePasswordUpdated"
    />

    <UpdateCompanyDataModal 
      :show="showCompanyDataModal" 
      :company="company"
      @close="showCompanyDataModal = false"
      @company-data-updated="handleCompanyDataUpdated"
    />
  </AppLayout>
</template>

<script setup>
import PhotoUpload from '@/Components/PhotoUpload.vue'
import UpdateCompanyDataModal from '@/Components/SettingsModals/UpdateCompanyDataModal.vue'
import UpdatePasswordModal from '@/Components/SettingsModals/UpdatePasswordModal.vue'
import UpdatePersonalDataModal from '@/Components/SettingsModals/UpdatePersonalDataModal.vue'
import Button from '@/Components/Button.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import { usePhotoUrl } from '@/composables/usePhotoUrl'
import { useToast } from '@/composables/useToast'
import { useUserEvents } from '@/composables/useEvents'
import { router, usePage } from '@inertiajs/vue3'
import { onMounted, ref, computed } from 'vue'

const { success, error: showError } = useToast()
const { getPhotoUrl } = usePhotoUrl()
const { emitPhotoUpdated, onPhotoUpdated } = useUserEvents()
const page = usePage()

const props = defineProps({
  company: {
    type: Object,
    default: () => ({})
  }
})

const user = computed(() => page.props.auth.user)
const company = computed(() => props.company)
const activeSection = ref('personal')
const userPhoto = ref(user.value?.photo_key || null)
const currentPhotoUrl = ref(null)

// Estados dos modais
const showPersonalDataModal = ref(false)
const showPasswordModal = ref(false)
const showCompanyDataModal = ref(false)

const breadcrumbItems = [
  { label: 'Início', href: '/' },
  { label: 'Configurações' }
]

const loadUserPhotoUrl = async () => {
  if (userPhoto.value) {
    return await getPhotoUrl(userPhoto.value)
  }
  return null
}

// Função para atualizar a foto localmente
const updateLocalPhoto = async (photoKey, photoUrl) => {
  userPhoto.value = photoKey
  currentPhotoUrl.value = photoUrl
  emitPhotoUpdated(photoUrl)
}

// Funções para upload de foto
const handlePhotoUploadSuccess = async (data) => {
  if (data) {
    try {
      const response = await fetch(`/users/${user.value.id}/photo`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': page.props.csrf_token,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          photo_key: data.key
        })
      })

      if (response.ok) {
        const result = await response.json()
        if (result.success) {
          updateLocalPhoto(data.key, data.url)
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

// Escutar eventos de atualização da foto
onPhotoUpdated((event) => {
  if (event.detail.url) {
    currentPhotoUrl.value = event.detail.url
  }
})

onMounted(async () => {
  if (userPhoto.value) {
    currentPhotoUrl.value = await loadUserPhotoUrl()
  }
})

// Funções de callback para os modais
const handlePersonalDataUpdated = (updatedUser) => {
  if (updatedUser) {
    router.reload()
  }
}

const handlePasswordUpdated = () => {
  // Senha foi alterada com sucesso
}

const handleCompanyDataUpdated = (updatedCompany) => {
  if (updatedCompany) {
    router.reload()
  }
}
</script>