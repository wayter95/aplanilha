<template>
  <div class="tabs-container">
    <!-- Tab Headers -->
    <div class="tabs-border-bottom">
      <nav class="-mb-0.5 sm:flex sm:space-x-6 rtl:space-x-reverse" role="tablist">
        <button
          v-for="(tab, index) in tabs"
          :key="tab.id"
          type="button"
          :class="getTabButtonClasses(tab.id)"
          :id="`tab-button-${tab.id}`"
          @click="selectTab(tab.id)"
          :aria-controls="`tab-panel-${tab.id}`"
          :aria-selected="activeTabId === tab.id"
          role="tab"
        >
          <i v-if="tab.icon" :class="[tab.icon, 'me-2']"></i>
          {{ tab.label }}
          <span v-if="tab.badge" class="ms-2 inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium bg-primary/10 text-primary">
            {{ tab.badge }}
          </span>
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content mt-4">
      <div
        v-for="tab in tabs"
        :key="tab.id"
        :id="`tab-panel-${tab.id}`"
        :class="{ 'hidden': activeTabId !== tab.id }"
        role="tabpanel"
        :aria-labelledby="`tab-button-${tab.id}`"
      >
        <slot :name="`tab-${tab.id}`" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  tabs: {
    type: Array,
    required: true,
    validator: (tabs) => {
      return tabs.every(tab => tab.id && tab.label);
    }
  },
  modelValue: {
    type: String,
    default: null
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'pills', 'underline', 'outline'].includes(value)
  },
  vertical: {
    type: Boolean,
    default: false
  },
  justified: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const activeTabId = ref(props.modelValue || props.tabs[0]?.id);

const selectTab = (tabId) => {
  activeTabId.value = tabId;
  emit('update:modelValue', tabId);
  emit('change', tabId);
};

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue !== activeTabId.value) {
    activeTabId.value = newValue;
  }
});

// Computed Classes
const getTabButtonClasses = (tabId) => {
  const isActive = activeTabId.value === tabId;
  const baseClasses = [
    'w-full',
    'sm:w-auto',
    'py-4',
    'px-1',
    'inline-flex',
    'items-center',
    'gap-2',
    'text-sm',
    'whitespace-nowrap',
    'transition-all',
    'duration-200'
  ];
  
  if (isActive) {
    baseClasses.push('active');
  }
  
  return baseClasses.join(' ');
};
</script>

<style scoped>
.tabs-container {
  display: block;
  width: 100%;
}

/* Borda principal das tabs */
.tabs-border-bottom {
  border-bottom: 2px solid #e2e8f0;
}

:global(.dark) .tabs-border-bottom {
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

/* Estilos dos botões das tabs */
.tabs-container button[role="tab"] {
  position: relative;
  color: #64748b;
  font-weight: 500;
}

:global(.dark) .tabs-container button[role="tab"] {
  color: #8c9097;
}

/* Tab ativa */
.tabs-container button[role="tab"].active {
  color: #845adf;
  font-weight: 600;
}

/* Hover */
.tabs-container button[role="tab"]:hover {
  color: #845adf;
}

/* Garantir que a borda da tab ativa seja visível */
.tabs-container button[role="tab"].active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #845adf;
}
</style>
