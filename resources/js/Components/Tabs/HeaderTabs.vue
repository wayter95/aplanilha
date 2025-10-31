<template>
  <div class="flex gap-2 items-center h-full">
    <div
      v-for="tab in tabs"
      :key="tab.key"
      class="flex items-center gap-2 px-3 py-1.5 rounded-md cursor-pointer transition-all whitespace-nowrap h-8 border"
      :class="[
        activeTab?.key === tab.key
          ? 'bg-primary-500 dark:bg-primary-600 text-white font-semibold shadow-sm border-primary-400 dark:border-primary-500'
          : 'bg-white/10 dark:bg-gray-700/50 text-white/90 dark:text-gray-200 hover:bg-white/20 dark:hover:bg-gray-600 border-white/20 dark:border-gray-600',
      ]"
      @click="handleSelect(tab)"
    >
      <i class="bx bx-file text-sm"></i>
      <span class="truncate max-w-[180px] text-sm font-medium">{{ tab.title }}</span>
      <button
        class="ml-1 text-xs text-red-400 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 transition-colors flex items-center justify-center w-4 h-4 rounded hover:bg-red-500/20"
        @click.stop="handleClose(tab)"
        title="Fechar"
      >
        ✕
      </button>
    </div>
  </div>
  <div v-if="showLimitWarning" class="ml-4 px-3 py-1 text-xs text-warning bg-warning/20 dark:bg-warning/30 rounded">
    Limite de abas atingido.
  </div>
</template>

<script setup>
import { UI_CONFIG } from '@/config/ui'
import { useTabsStore } from '@/stores/useTabsStore'
import { router } from '@inertiajs/vue3'
import { storeToRefs } from 'pinia'
import { computed } from 'vue'

const emit = defineEmits(['select', 'close'])

const tabsStore = useTabsStore()
const { tabs, activeTab } = storeToRefs(tabsStore)

const showLimitWarning = computed(() => (tabs.value?.length || 0) >= UI_CONFIG.MAX_TABS)

const handleSelect = (tab) => {
  tabsStore.setActive(tab)
  // Navegar para a rota da aba, se existir
  const path = tab.path || (tab.mode === 'create' ? `/document-templates/new/${tab.key}` : `/document-templates/${tab.key}/edit`)
  if (path) {
    router.visit(path)
  }
  emit('select', tab)
}

const handleClose = (tab) => {
  const wasActive = activeTab.value?.key === tab.key
  tabsStore.closeTab(tab)
  // Se a aba fechada era a ativa, navega para a listagem de modelos
  if (wasActive) {
    router.visit('/document-templates')
  }
  emit('close', tab)
}
</script>


