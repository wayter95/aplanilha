<template>
  <div class="project-workspace-container">
    <!-- Left Navigation Sidebar -->
    <div class="project-workspace-navigation">
      <div class="flex items-center justify-between w-full p-4 border-b dark:border-defaultborder/10">
        <div>
          <h6 class="font-semibold mb-0 text-[1rem] text-defaulttextcolor">{{ project?.name }}</h6>
          <p class="text-[0.75rem] text-gray-500 dark:text-gray-400 mt-1">{{ project?.client }}</p>
        </div>
        <div class="hs-dropdown ti-dropdown">
          <button class="ti-btn btn-wave ti-btn-sm ti-btn-primary" aria-label="button" type="button" aria-expanded="false">
            <i class="ri-settings-3-line"></i>
          </button>
          <ul class="hs-dropdown-menu ti-dropdown-menu hidden">
            <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium" href="javascript:void(0);">Editar Projeto</a></li>
            <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium" href="javascript:void(0);">Arquivar</a></li>
            <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium" href="javascript:void(0);">Deletar</a></li>
          </ul>
        </div>
      </div>

      <!-- Search Section -->
      <div class="p-4 border-b dark:border-defaultborder/10">
        <div class="input-group">
          <input 
            type="text" 
            class="form-control !bg-light border-0 !rounded-s-sm" 
            placeholder="Buscar no projeto..." 
            aria-describedby="search-project"
            v-model="searchQuery"
          >
          <button aria-label="button" type="button" class="ti-btn ti-btn-light !rounded-s-none !mb-0" id="search-project">
            <i class="ri-search-line text-[#8c9097] dark:text-white/50"></i>
          </button>
        </div>
      </div>

      <!-- Navigation Menu -->
      <div>
        <ul class="list-none workspace-nav" id="workspace-nav">
          <!-- Resumo do Projeto -->
          <li 
            v-for="section in navigationSections" 
            :key="section.id"
            :class="['workspace-section', { active: activeSection === section.id }]"
          >
            <a href="javascript:void(0)" @click="handleSectionChange(section.id)">
              <div class="flex items-center">
                <div class="me-2">
                  <i :class="[section.icon, 'text-[1rem]']"></i>
                </div>
                <span class="flex-grow whitespace-nowrap">
                  {{ section.label }}
                </span>
                <span v-if="section.badge" class="badge bg-primary text-white">{{ section.badge }}</span>
              </div>
            </a>
          </li>

          <!-- Separador -->
          <li class="my-2 border-t dark:border-defaultborder/10"></li>

          <!-- Configurações -->
          <li class="workspace-section">
            <a href="javascript:void(0)">
              <div class="flex items-center">
                <div class="me-2">
                  <i class="ri-settings-3-line text-[1rem]"></i>
                </div>
                <span class="flex-grow whitespace-nowrap">
                  Configurações
                </span>
              </div>
            </a>
          </li>

          <li class="workspace-section">
            <a href="javascript:void(0)">
              <div class="flex items-center">
                <div class="me-2">
                  <i class="ri-question-line text-[1rem]"></i>
                </div>
                <span class="flex-grow whitespace-nowrap">
                  Ajuda
                </span>
              </div>
            </a>
          </li>

          <!-- Projeto Info -->
          <li class="mb-8 mt-8 px-4 py-4 border-t dark:border-defaultborder/10">
            <div class="text-[#8c9097] dark:text-white/50 mb-3">
              <p class="text-[.875rem] font-bold mb-2">{{ project.status }}</p>
              <p class="text-[.75rem] mb-0">{{ formatDate(project.start_date) }} até {{ formatDate(project.end_date) }}</p>
            </div>
            <div class="progress progress-xs">
              <div :class="['progress-bar', progressBarClass]" :style="{ width: projectProgress + '%' }" :aria-valuenow="projectProgress" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="text-[.75rem] text-[#8c9097] dark:text-white/50 mt-2">{{ projectProgress }}% Completo</p>
          </li>

          <!-- Team Info -->
          <li class="mb-8 border-t dark:border-defaultborder/10 pt-4 px-4">
            <p class="text-[.875rem] font-semibold mb-3">Equipe</p>
            <div class="flex items-center mb-2">
              <span class="avatar avatar-sm me-2">
                <img src="https://ui-avatars.com/api/?name=Team" alt="" class="!rounded-md">
              </span>
              <div class="flex-grow">
                <p class="text-[.75rem] font-semibold mb-0">{{ project.responsible }}</p>
                <p class="text-[.7rem] text-gray-500">Responsável</p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Central Content Area -->
    <div class="project-workspace-content">
      <!-- Header com Ações -->
      <div class="flex p-4 flex-wrap gap-2 items-center justify-between border-b dark:border-defaultborder/10">
        <div>
          <h6 class="font-semibold mb-0 text-[1rem]">{{ getCurrentSectionTitle() }}</h6>
        </div>
        <div class="flex gap-2">
          <button 
            aria-label="button" 
            type="button" 
            id="workspace-close-btn" 
            class="sm:hidden block btn btn-icon btn-sm btn-danger"
            @click="closeMobileView"
          >
            <i class="ri-close-fill"></i>
          </button>
          
          <!-- Action Buttons por seção -->
          <div v-if="activeSection === 'overview'" class="flex gap-2">
            <button class="ti-btn btn-wave !gap-0 !py-1 !px-2 !text-[0.75rem] !font-medium bg-primary text-white flex items-center justify-center">
              <i class="ri-download-line align-middle !me-1"></i>Exportar
            </button>
          </div>

          <div v-if="activeSection === 'budgets'" class="flex gap-2">
            <button 
              class="ti-btn btn-wave !gap-0 !py-1 !px-2 !text-[0.75rem] !font-medium bg-primary text-white flex items-center justify-center"
              @click="showAddBudgetModal = true"
            >
              <i class="ri-add-circle-line align-middle !me-1"></i>Novo Orçamento
            </button>
          </div>

          <div v-if="activeSection === 'files'" class="flex gap-2">
            <button 
              class="ti-btn btn-wave !gap-0 !py-1 !px-2 !text-[0.75rem] !font-medium bg-primary text-white flex items-center justify-center"
              @click="showUploadModal = true"
            >
              <i class="ri-upload-cloud-line align-middle !me-1"></i>Enviar Arquivo
            </button>
          </div>

          <div v-if="activeSection === 'balance'" class="flex gap-2">
            <button class="ti-btn btn-wave !gap-0 !py-1 !px-2 !text-[0.75rem] !font-medium bg-success text-white flex items-center justify-center">
              <i class="ri-file-pdf-line align-middle !me-1"></i>Gerar PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Dynamic Content Area -->
      <div class="p-4 workspace-content-area">
        <!-- Overview Section -->
        <div v-if="activeSection === 'overview'" class="space-y-6">
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card: Orçamentos -->
            <div class="stats-card stats-card-primary">
              <div class="flex items-center justify-between">
                <div>
                  <p class="stats-label">Orçamentos</p>
                  <p class="stats-value text-primary">{{ project.budgets?.length || 0 }}</p>
                </div>
                <i class="ri-money-dollar-circle-line stats-icon text-primary/30"></i>
              </div>
            </div>

            <!-- Card: Arquivos -->
            <div class="stats-card stats-card-success">
              <div class="flex items-center justify-between">
                <div>
                  <p class="stats-label">Arquivos</p>
                  <p class="stats-value text-success">{{ project.files?.length || 0 }}</p>
                </div>
                <i class="ri-file-list-line stats-icon text-success/30"></i>
              </div>
            </div>

            <!-- Card: Equipe -->
            <div class="stats-card stats-card-info">
              <div class="flex items-center justify-between">
                <div>
                  <p class="stats-label">Equipe</p>
                  <p class="stats-value text-info">{{ project.team?.length || 1 }}</p>
                </div>
                <i class="ri-team-line stats-icon text-info/30"></i>
              </div>
            </div>

            <!-- Card: Progresso -->
            <div class="stats-card stats-card-warning">
              <div class="flex items-center justify-between">
                <div>
                  <p class="stats-label">Progresso</p>
                  <p class="stats-value text-warning">{{ projectProgress }}%</p>
                </div>
                <i class="ri-progress-5-line stats-icon text-warning/30"></i>
              </div>
            </div>
          </div>

          <!-- Timeline / Atividades Recentes -->
          <div class="activities-card">
            <div class="activities-header">
              <h5 class="activities-title">Atividades Recentes</h5>
            </div>
            <div class="activities-body">
              <div class="space-y-3">
                <div class="activity-item">
                  <div class="activity-marker activity-marker-primary"></div>
                  <div class="activity-content">
                    <p class="activity-text">Orçamento criado</p>
                    <p class="activity-time">há 2 horas</p>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-marker activity-marker-success"></div>
                  <div class="activity-content">
                    <p class="activity-text">Arquivo adicionado</p>
                    <p class="activity-time">há 1 dia</p>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-marker activity-marker-warning"></div>
                  <div class="activity-content">
                    <p class="activity-text">Status atualizado</p>
                    <p class="activity-time">há 3 dias</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Budgets Section -->
        <div v-if="activeSection === 'budgets'" class="space-y-4">
          <div v-if="!project?.budgets || project.budgets.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded">
            <i class="ri-file-list-3-line text-4xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Nenhum orçamento cadastrado</p>
            <p class="text-gray-400 text-sm mt-1">Clique no botão "Novo Orçamento" para começar</p>
          </div>

          <div v-else class="space-y-3">
            <div v-for="budget in project.budgets" :key="budget.id" class="box shadow-none border">
              <div class="box-body">
                <div class="flex items-center justify-between">
                  <div class="flex-grow">
                    <p class="font-semibold text-sm mb-1">{{ budget.description }}</p>
                    <p class="text-xs text-gray-500">{{ budget.date }}</p>
                  </div>
                  <div class="text-right">
                    <p class="font-bold text-lg text-primary">R$ {{ formatCurrency(budget.value) }}</p>
                    <span :class="['text-xs px-2 py-1 rounded', budget.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800']">
                      {{ budget.status === 'approved' ? 'Aprovado' : 'Pendente' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Files Section -->
        <div v-if="activeSection === 'files'" class="space-y-4">
          <div v-if="!project?.files || project.files.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded">
            <i class="ri-folder-open-line text-4xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Nenhum arquivo enviado</p>
            <p class="text-gray-400 text-sm mt-1">Clique no botão "Enviar Arquivo" para começar</p>
          </div>

          <div v-else class="space-y-2">
            <div v-for="file in project.files" :key="file.id" class="box shadow-none border hover:bg-gray-50 dark:hover:bg-gray-900 transition">
              <div class="box-body !p-3">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <i :class="['text-2xl', getFileIcon(file.type)]"></i>
                    <div>
                      <p class="font-semibold text-sm">{{ file.name }}</p>
                      <p class="text-xs text-gray-500">{{ file.size }} · {{ file.date }}</p>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <button class="text-blue-600 hover:text-blue-800"><i class="ri-download-line"></i></button>
                    <button class="text-red-600 hover:text-red-800"><i class="ri-delete-bin-line"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Balance Section -->
        <div v-if="activeSection === 'balance'" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="box">
              <div class="box-body">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-2">Total Orçado</p>
                <p class="text-2xl font-bold text-primary">R$ {{ calculateTotal('budget') }}</p>
              </div>
            </div>
            <div class="box">
              <div class="box-body">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-2">Total Gasto</p>
                <p class="text-2xl font-bold text-danger">R$ {{ calculateTotal('spent') }}</p>
              </div>
            </div>
            <div class="box">
              <div class="box-body">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-2">Saldo Disponível</p>
                <p class="text-2xl font-bold text-success">R$ {{ calculateTotal('balance') }}</p>
              </div>
            </div>
          </div>

          <!-- Tabela de Movimentações -->
          <div class="box">
            <div class="box-header">
              <h5 class="box-title">Movimentações Financeiras</h5>
            </div>
            <div class="box-body">
              <div class="overflow-x-auto">
                <table class="table-auto w-full text-sm">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-3 py-2 text-left">Data</th>
                      <th class="px-3 py-2 text-left">Descrição</th>
                      <th class="px-3 py-2 text-right">Valor</th>
                      <th class="px-3 py-2 text-center">Tipo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="transaction in project?.transactions" :key="transaction.id" class="border-b dark:border-defaultborder/10 hover:bg-gray-50 dark:hover:bg-gray-900">
                      <td class="px-3 py-2">{{ transaction.date }}</td>
                      <td class="px-3 py-2">{{ transaction.description }}</td>
                      <td class="px-3 py-2 text-right font-semibold" :class="transaction.type === 'income' ? 'text-success' : 'text-danger'">
                        {{ transaction.type === 'income' ? '+' : '-' }} R$ {{ formatCurrency(transaction.value) }}
                      </td>
                      <td class="px-3 py-2 text-center">
                        <span :class="['text-xs px-2 py-1 rounded', transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                          {{ transaction.type === 'income' ? 'Entrada' : 'Saída' }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Team Section -->
        <div v-if="activeSection === 'team'" class="space-y-4">
          <div v-if="!project?.team || project.team.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded">
            <i class="ri-team-line text-4xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Nenhum membro na equipe</p>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="member in project.team" :key="member.id" class="box shadow-none border">
              <div class="box-body">
                <div class="flex items-center gap-3 mb-3">
                  <span class="avatar avatar-md">
                    <img :src="member.avatar" :alt="member.name" class="!rounded-md">
                  </span>
                  <div class="flex-grow">
                    <p class="font-semibold text-sm">{{ member.name }}</p>
                    <p class="text-xs text-gray-500">{{ member.role }}</p>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 ti-btn !py-1 !px-2 !text-[0.75rem] ti-btn-light">Contatar</button>
                  <button class="flex-1 ti-btn !py-1 !px-2 !text-[0.75rem] ti-btn-outline-danger">Remover</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Sidebar - Details Panel -->
    <div class="workspace-details-panel">
      <div class="flex p-4 items-center justify-between border-b dark:border-defaultborder/10">
        <div>
          <h6 class="font-semibold mb-0 text-[1rem]">Detalhes</h6>
        </div>
        <button 
          aria-label="button" 
          type="button" 
          id="details-close-btn" 
          class="ti-btn ti-btn-icon ti-btn-sm ti-btn-danger xl:hidden block"
          @click="closeDetailsPanel"
        >
          <i class="ri-close-fill"></i>
        </button>
      </div>

      <div class="workspace-details-content" id="workspace-details">
        <div class="p-4">
          <!-- Project Details -->
          <div class="mb-6">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-3">Informações do Projeto</p>
            <ul class="space-y-3">
              <li class="flex justify-between">
                <span class="text-sm font-medium">Status:</span>
                <span class="text-sm text-gray-600">{{ project?.status }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-sm font-medium">Cliente:</span>
                <span class="text-sm text-gray-600">{{ project?.client }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-sm font-medium">Início:</span>
                <span class="text-sm text-gray-600">{{ formatDate(project?.start_date) }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-sm font-medium">Término:</span>
                <span class="text-sm text-gray-600">{{ formatDate(project?.end_date) }}</span>
              </li>
            </ul>
          </div>

          <!-- Statistics -->
          <div class="border-t dark:border-defaultborder/10 pt-4 mb-6">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-3">Estatísticas</p>
            <div class="space-y-3">
              <div>
                <div class="flex justify-between mb-1">
                  <span class="text-xs font-medium">Progresso</span>
                  <span class="text-xs font-semibold">{{ projectProgress }}%</span>
                </div>
                <div class="progress progress-sm">
                  <div :class="['progress-bar', progressBarClass]" :style="{ width: projectProgress + '%' }"></div>
                </div>
              </div>
              <div class="flex justify-between pt-2">
                <span class="text-xs">Orçamentos:</span>
                <span class="font-semibold text-xs">{{ project?.budgets?.length || 0 }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-xs">Arquivos:</span>
                <span class="font-semibold text-xs">{{ project?.files?.length || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Tags -->
          <div class="border-t dark:border-defaultborder/10 pt-4">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-3">Tags</p>
            <div class="flex flex-wrap gap-2">
              <span v-for="tag in project?.tags" :key="tag" class="badge bg-primary/10 text-primary">{{ tag }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  project: {
    type: Object,
    required: true
  }
})

const activeSection = ref('overview')
const searchQuery = ref('')
const showAddBudgetModal = ref(false)
const showUploadModal = ref(false)

const navigationSections = [
  { id: 'overview', label: 'Visão Geral', icon: 'ri-dashboard-line', badge: null },
  { id: 'budgets', label: 'Orçamentos', icon: 'ri-money-dollar-circle-line', badge: computed(() => props.project?.budgets?.length || 0).value },
  { id: 'files', label: 'Arquivos', icon: 'ri-folder-line', badge: computed(() => props.project?.files?.length || 0).value },
  { id: 'balance', label: 'Balanço Financeiro', icon: 'ri-bar-chart-line', badge: null },
  { id: 'team', label: 'Equipe', icon: 'ri-team-line', badge: computed(() => props.project?.team?.length || 0).value }
]

const projectProgress = computed(() => {
  return props.project?.progress || 0
})

const progressBarClass = computed(() => {
  if (projectProgress.value >= 75) return 'bg-success'
  if (projectProgress.value >= 50) return 'bg-info'
  if (projectProgress.value >= 25) return 'bg-warning'
  return 'bg-danger'
})

const handleSectionChange = (sectionId) => {
  activeSection.value = sectionId
}

const getCurrentSectionTitle = () => {
  const section = navigationSections.find(s => s.id === activeSection.value)
  return section?.label || 'Visão Geral'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('pt-BR')
}

const formatCurrency = (value) => {
  if (!value) return '0,00'
  return parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const calculateTotal = (type) => {
  if (type === 'budget') {
    const total = (props.project?.budgets || []).reduce((sum, b) => sum + (b.value || 0), 0)
    return formatCurrency(total)
  }
  if (type === 'spent') {
    const total = (props.project?.transactions || [])
      .filter(t => t.type === 'expense')
      .reduce((sum, t) => sum + (t.value || 0), 0)
    return formatCurrency(total)
  }
  if (type === 'balance') {
    const budgetTotal = (props.project?.budgets || []).reduce((sum, b) => sum + (b.value || 0), 0)
    const spentTotal = (props.project?.transactions || [])
      .filter(t => t.type === 'expense')
      .reduce((sum, t) => sum + (t.value || 0), 0)
    return formatCurrency(budgetTotal - spentTotal)
  }
  return '0,00'
}

const getFileIcon = (fileType) => {
  const icons = {
    pdf: 'ri-file-pdf-line text-danger',
    doc: 'ri-file-word-line text-blue-600',
    xls: 'ri-file-excel-line text-green-600',
    zip: 'ri-file-zip-line text-orange-600',
    image: 'ri-image-line text-purple-600',
    video: 'ri-video-line text-pink-600',
    default: 'ri-file-line text-gray-600'
  }
  return icons[fileType] || icons.default
}

const closeMobileView = () => {
  // Implementar lógica para fechar view mobile
}

const closeDetailsPanel = () => {
  // Implementar lógica para fechar painel de detalhes
}
</script>

<style scoped>
.project-workspace-container {
  display: flex;
  gap: 1rem;
  background-color: transparent;
  min-height: calc(100vh - 200px);
}

.project-workspace-navigation {
  width: 280px;
  background: var(--box-background, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.5rem;
  overflow-y: auto;
  flex-shrink: 0;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.project-workspace-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--box-background, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.5rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.workspace-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.workspace-section {
  margin: 0;
  padding: 0;
}

.workspace-section a {
  display: flex;
  align-items: center;
  padding: 0.625rem 1rem;
  color: var(--text-color, #374151);
  text-decoration: none;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
  font-size: 0.875rem;
  gap: 0.5rem;
}

.workspace-section a:hover {
  background-color: var(--hover-bg, #f3f4f6);
  border-left-color: var(--primary-color, #3b82f6);
}

.workspace-section.active a {
  background-color: var(--primary-bg, #eff6ff);
  border-left-color: var(--primary-color, #3b82f6);
  color: var(--primary-color, #3b82f6);
  font-weight: 600;
}

.workspace-details-panel {
  width: 300px;
  background: var(--box-background, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.5rem;
  overflow-y: auto;
  flex-shrink: 0;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.workspace-content-area {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

/* Box styles fix */
:deep(.box) {
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.5rem;
  margin-bottom: 0;
  background: transparent;
}

:deep(.box.shadow-none) {
  box-shadow: none;
}

:deep(.box-body) {
  padding: 1rem;
}

:deep(.box-header) {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color, #e5e7eb);
}

:deep(.box-title) {
  margin: 0;
  font-size: 1rem;
}

/* Progress bar */
:deep(.progress) {
  background-color: var(--progress-bg, #e5e7eb);
  border-radius: 0.5rem;
  height: 0.5rem;
  overflow: hidden;
}

:deep(.progress-bar) {
  background-color: var(--primary-color, #3b82f6);
  height: 100%;
  transition: width 0.3s ease;
}

/* Progress bar colors */
:deep(.progress-bar[class*="bg-success"]) {
  background-color: #10b981;
}

:deep(.progress-bar[class*="bg-info"]) {
  background-color: #0ea5e9;
}

:deep(.progress-bar[class*="bg-warning"]) {
  background-color: #f59e0b;
}

:deep(.progress-bar[class*="bg-danger"]) {
  background-color: #ef4444;
}

/* Stats Cards - Overview */
.stats-card {
  padding: 1.25rem;
  border-radius: 0.75rem;
  border: 2px solid;
  background: white;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Primary Card - Orçamentos */
.stats-card-primary {
  border-color: #dbeafe;
  background: linear-gradient(135deg, #f0f9ff 0%, white 100%);
}

.stats-card-primary:hover {
  border-color: #93c5fd;
  background: linear-gradient(135deg, #e0f2fe 0%, white 100%);
}

/* Success Card - Arquivos */
.stats-card-success {
  border-color: #dcfce7;
  background: linear-gradient(135deg, #f0fdf4 0%, white 100%);
}

.stats-card-success:hover {
  border-color: #86efac;
  background: linear-gradient(135deg, #dcfce7 0%, white 100%);
}

/* Info Card - Equipe */
.stats-card-info {
  border-color: #cffafe;
  background: linear-gradient(135deg, #f0f9ff 0%, white 100%);
}

.stats-card-info:hover {
  border-color: #67e8f9;
  background: linear-gradient(135deg, #ecf9ff 0%, white 100%);
}

/* Warning Card - Progresso */
.stats-card-warning {
  border-color: #fef3c7;
  background: linear-gradient(135deg, #fffbeb 0%, white 100%);
}

.stats-card-warning:hover {
  border-color: #fcd34d;
  background: linear-gradient(135deg, #fef9e7 0%, white 100%);
}

.stats-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--text-secondary, #6b7280);
  margin-bottom: 0.5rem;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  display: block;
}

.stats-value {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
  display: block;
}

.stats-icon {
  font-size: 2.5rem;
  opacity: 0.15;
  margin-left: auto;
}

/* Dark Mode for Stats Cards */
:deep(.dark) .stats-card {
  background: var(--dark-card-bg, #1f2937);
  color: var(--dark-text, #f3f4f6);
}

:deep(.dark) .stats-card-primary {
  border-color: #1e3a8a;
  background: linear-gradient(135deg, rgba(30, 58, 138, 0.1) 0%, var(--dark-card-bg, #1f2937) 100%);
}

:deep(.dark) .stats-card-success {
  border-color: #064e3b;
  background: linear-gradient(135deg, rgba(6, 78, 59, 0.1) 0%, var(--dark-card-bg, #1f2937) 100%);
}

:deep(.dark) .stats-card-info {
  border-color: #0c4a6e;
  background: linear-gradient(135deg, rgba(12, 74, 110, 0.1) 0%, var(--dark-card-bg, #1f2937) 100%);
}

:deep(.dark) .stats-card-warning {
  border-color: #78350f;
  background: linear-gradient(135deg, rgba(120, 53, 15, 0.1) 0%, var(--dark-card-bg, #1f2937) 100%);
}

:deep(.dark) .stats-label {
  color: var(--dark-text-secondary, #9ca3af);
}

/* Activities Card */
.activities-card {
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.75rem;
  background: white;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.activities-header {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color, #e5e7eb);
  background: var(--header-bg, #f9fafb);
}

.activities-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary, #1f2937);
}

.activities-body {
  padding: 1rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding-bottom: 1rem;
}

.activity-item:last-child {
  padding-bottom: 0;
}

.activity-marker {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 50%;
  margin-top: 0.375rem;
  flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8);
}

.activity-marker-primary {
  background-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.activity-marker-success {
  background-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.activity-marker-warning {
  background-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.activity-content {
  flex: 1;
}

.activity-text {
  margin: 0;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary, #1f2937);
}

.activity-time {
  margin: 0.25rem 0 0 0;
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
}

/* Dark Mode for Activities */
:deep(.dark) .activities-card {
  background: var(--dark-card-bg, #1f2937);
  border-color: var(--dark-border, #374151);
}

:deep(.dark) .activities-header {
  border-color: var(--dark-border, #374151);
  background: var(--dark-header-bg, #111827);
}

:deep(.dark) .activities-title {
  color: var(--dark-text, #f3f4f6);
}

:deep(.dark) .activity-text {
  color: var(--dark-text, #f3f4f6);
}

:deep(.dark) .activity-time {
  color: var(--dark-text-secondary, #9ca3af);
}

/* Dark Mode */
:deep(.dark) .project-workspace-navigation,
:deep(.dark) .project-workspace-content,
:deep(.dark) .workspace-details-panel {
  background-color: var(--dark-bg, #1f2937);
  color: var(--dark-text, #f3f4f6);
  border-color: var(--dark-border, #374151);
}

:deep(.dark) .workspace-section a {
  color: var(--dark-text, #f3f4f6);
}

:deep(.dark) .workspace-section a:hover {
  background-color: var(--dark-hover-bg, #374151);
}

:deep(.dark) .workspace-section.active a {
  background-color: var(--dark-primary-bg, #1e40af);
}

:deep(.dark) .box {
  background-color: var(--dark-card-bg, #111827);
  border-color: var(--dark-border, #374151);
}

/* Responsive */
@media (max-width: 1280px) {
  .project-workspace-navigation {
    width: 240px;
  }

  .workspace-details-panel {
    width: 260px;
  }
}

@media (max-width: 1024px) {
  .project-workspace-container {
    gap: 0.75rem;
  }

  .project-workspace-navigation {
    width: 200px;
  }

  .workspace-details-panel {
    width: 220px;
  }

  .workspace-content-area {
    padding: 1rem;
  }
}

@media (max-width: 768px) {
  .project-workspace-container {
    flex-direction: column;
  }

  .project-workspace-navigation,
  .workspace-details-panel {
    width: 100%;
    max-height: 250px;
    border-radius: 0.5rem;
  }

  .project-workspace-content {
    flex: 1;
    min-height: 400px;
  }

  .workspace-content-area {
    padding: 1rem;
  }
}

/* Badge styles */
:deep(.badge) {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

:deep(.badge.bg-primary) {
  background-color: var(--primary-color, #3b82f6);
  color: white;
}

/* Avatar */
:deep(.avatar) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
}

:deep(.avatar-sm) {
  width: 2rem;
  height: 2rem;
}

:deep(.avatar-md) {
  width: 3rem;
  height: 3rem;
}

:deep(.avatar img) {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
