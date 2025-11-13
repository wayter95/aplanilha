<template>
  <!-- Desktop - Inline Tabs -->
  <div v-if="!mobile && tabs.length > 0" class="tab-bar-inline">
    <div class="flex items-center overflow-x-auto scrollbar-thin">
      <div
        v-for="tab in tabs"
        :key="tab.key"
        @click="activateTab(tab)"
        class="tab-item group"
        :class="{
          'tab-active': isActive(tab)
        }"
      >
        <span class="tab-title">{{ tab.title }}</span>
        <button
          @click.stop="closeTab(tab)"
          class="tab-close"
          :title="`Fechar ${tab.title}`"
        >
          <i class="ri-close-line"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile - Dropdown -->
  <div v-if="mobile && tabs.length > 0" class="hs-dropdown ti-dropdown">
    <button
      type="button"
      class="hs-dropdown-toggle ti-dropdown-toggle !p-0 !border-0 flex-shrink-0"
      aria-label="Tabs abertas"
    >
      <i class="bx bx-tab header-link-icon"></i>
      <span v-if="tabs.length > 0" class="badge bg-primary rounded-full !px-1.5 !py-0.5 !text-[0.65rem] absolute -top-1 -right-1">
        {{ tabs.length }}
      </span>
    </button>
    <div class="hs-dropdown-menu ti-dropdown-menu !min-w-[16rem]">
      <div class="ti-dropdown-header !bg-primary text-white">
        <p class="ti-dropdown-header-title !text-white">
          Abas Abertas ({{ tabs.length }})
        </p>
      </div>
      <div class="ti-dropdown-divider"></div>
      <a
        v-for="tab in tabs"
        :key="tab.key"
        href="javascript:void(0);"
        @click="activateTab(tab)"
        class="ti-dropdown-item group"
        :class="{ '!bg-primary/10': isActive(tab) }"
      >
        <div class="flex items-center justify-between w-full">
          <span :class="{ 'font-semibold text-primary': isActive(tab) }">
            {{ tab.title }}
          </span>
          <button
            @click.stop="closeTab(tab)"
            class="opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded"
          >
            <i class="ri-close-line text-sm"></i>
          </button>
        </div>
      </a>
    </div>
  </div>
</template>

<script setup>
import { useTabsStore } from '@/stores/useTabsStore'
import { storeToRefs } from 'pinia'
import { router } from '@inertiajs/vue3'

defineProps({
  inline: {
    type: Boolean,
    default: false
  },
  mobile: {
    type: Boolean,
    default: false
  }
})

const tabsStore = useTabsStore()
const { tabs, activeTab } = storeToRefs(tabsStore)

const isActive = (tab) => {
  return activeTab.value?.key === tab.key
}

const activateTab = (tab) => {
  tabsStore.setActive(tab)
  router.visit(tab.path)
}

const closeTab = async (tab) => {
  const wasActive = isActive(tab)
  const closed = await tabsStore.closeTab(tab)
  
  if (closed && wasActive) {
    // Se fechou a tab ativa e não há mais tabs, volta para a listagem
    if (tabs.value.length === 0) {
      const basePath = tab.path.split('/').slice(0, -2).join('/')
      router.visit(basePath || '/')
    }
    // Se ainda há tabs, a store já ativou outra automaticamente
  }
}
</script>

<style scoped>
/* Desktop Inline Tabs */
.tab-bar-inline {
  height: 2.5rem;
  display: flex;
  align-items: center;
}

.tab-bar-inline > div {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
  height: 100%;
  display: flex;
  align-items: center;
}

.tab-bar-inline > div::-webkit-scrollbar {
  height: 3px;
}

.tab-bar-inline > div::-webkit-scrollbar-track {
  background: transparent;
}

.tab-bar-inline > div::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.3);
  border-radius: 4px;
}

.tab-bar-inline > div::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.5);
}

.tab-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  position: relative;
  min-width: 90px;
  max-width: 160px;
  background: transparent;
  height: 100%;
  border-right: 1px solid rgba(var(--default-border), 0.2);
  flex-shrink: 0;
}

.tab-item:hover {
  background: rgba(var(--primary-rgb), 0.05);
}

.tab-item.tab-active {
  background: rgba(var(--primary-rgb), 0.1);
  position: relative;
}

.tab-item.tab-active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: rgb(var(--primary-rgb));
}

.tab-title {
  font-size: 0.8125rem;
  color: rgb(var(--default-text-color));
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  line-height: 1.2;
  font-weight: 400;
}

.tab-active .tab-title {
  font-weight: 600;
  color: rgb(var(--primary-rgb));
}

/* Mobile Dropdown Badge */
.badge {
  font-size: 0.65rem;
  line-height: 1;
}

.tab-close {
  width: 1rem;
  height: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.25rem;
  color: rgb(var(--text-muted));
  transition: all 0.2s ease;
  opacity: 0;
  flex-shrink: 0;
  font-size: 0.875rem;
}

.tab-item:hover .tab-close {
  opacity: 0.6;
}

.tab-active .tab-close {
  opacity: 0.6;
}

.tab-close:hover {
  background: rgba(0, 0, 0, 0.1);
  opacity: 1 !important;
  color: rgb(var(--default-text-color));
}

:root[data-theme-mode="dark"] .tab-close:hover {
  background: rgba(255, 255, 255, 0.1);
}
</style>
