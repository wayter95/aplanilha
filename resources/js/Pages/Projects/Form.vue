<template>
  <AppLayout title="" description="">
    <!-- Breadcrumb -->
    <Breadcrumb
      :title="pageTitle"
      :items="breadcrumbItems"
    />
    
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <!-- Project Workspace -->
        <ProjectWorkspace 
          :project="projectData"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import ProjectWorkspace from '@/Components/ProjectWorkspace.vue'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  project: {
    type: Object,
    default: null
  },
  projectTypes: {
    type: Array,
    default: () => []
  }
})

const pageTitle = computed(() => {
  return props.project ? 'Editar Projeto' : 'Novo Projeto'
})

const breadcrumbItems = computed(() => {
  const items = [
    { label: 'Início', href: '/' },
    { label: 'Projetos', href: '/projects' }
  ]
  
  if (props.project) {
    items.push({ label: 'Editar Projeto' })
  } else {
    items.push({ label: 'Novo Projeto' })
  }
  
  return items
})

// Preparar dados do projeto para o workspace
const projectData = computed(() => {
  return {
    id: props.project?.id,
    name: props.project?.name,
    client: props.project?.client,
    status: props.project?.status,
    responsible: props.project?.responsible,
    start_date: props.project?.start_date,
    end_date: props.project?.end_date,
    progress: props.project?.progress || 0,
    budgets: props.project?.budgets || [],
    files: props.project?.files || [],
    team: props.project?.team || [],
    transactions: props.project?.transactions || [],
    tags: props.project?.tags || []
  }
})

const handleCancel = () => {
  router.visit('/projects')
}
</script>

<style scoped>
/* Estilo global do workspace */
:deep(.project-workspace-container) {
  background-color: transparent;
  border-radius: 0.5rem;
}

/* Integração com o sistema */
:deep(.project-workspace-navigation),
:deep(.project-workspace-content),
:deep(.workspace-details-panel) {
  background-color: var(--card-bg, #ffffff);
  border-color: var(--border-color, #e5e7eb);
}

:deep(.dark) .project-workspace-navigation,
:deep(.dark) .project-workspace-content,
:deep(.dark) .workspace-details-panel {
  background-color: var(--dark-card-bg, #1f2937);
}

/* Ajustar spacing */
:deep(.workspace-content-area) {
  background-color: transparent;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  :deep(.project-workspace-navigation) {
    width: 240px;
  }

  :deep(.workspace-details-panel) {
    width: 280px;
  }
}

@media (max-width: 1024px) {
  :deep(.project-workspace-navigation) {
    width: 200px;
  }

  :deep(.workspace-details-panel) {
    width: 250px;
  }
}
</style>
