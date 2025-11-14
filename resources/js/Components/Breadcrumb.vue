<template>
  <div class="block justify-between page-header md:flex">
    <div>
      <h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white dark:hover:text-white text-[1.125rem] font-semibold">
        {{ title }}
      </h3>
      <p v-if="subtitle" class="text-[0.813rem] text-textmuted mt-1">
        {{ subtitle }}
      </p>
    </div>
    <ol class="flex items-center whitespace-nowrap min-w-0">
      <li
        v-for="(item, index) in items"
        :key="index"
        class="text-[0.813rem] ps-[0.5rem]"
        :class="{ 'font-semibold': index === items.length - 1 }"
      >
        <AppLink
          v-if="item.href && index < items.length - 1"
          :href="item.href"
          variant="breadcrumb"
        >
          {{ item.label }}
          <i class="ti ti-chevrons-right flex-shrink-0 text-[#8c9097] dark:text-white/50 px-[0.5rem] overflow-visible rtl:rotate-180"></i>
        </AppLink>
        <span
          v-else
          class="text-defaulttextcolor hover:text-primary dark:text-[#8c9097] dark:hover:text-primary"
          :class="{ 'font-semibold': index === items.length - 1 }"
        >
          {{ item.label }}
        </span>
      </li>
    </ol>
  </div>
</template>

<script setup>
import AppLink from './AppLink.vue'

defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  items: {
    type: Array,
    required: true,
    // Exemplo: [
    //   { label: 'Home', href: '/' },
    //   { label: 'Projetos', href: '/projects' },
    //   { label: 'Novo' }
    // ]
  }
})
</script>
