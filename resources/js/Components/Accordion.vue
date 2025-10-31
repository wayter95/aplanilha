<template>
  <div class="rounded-md border border-default-200 bg-white dark:bg-gray-800 shadow-sm">
    <button
      type="button"
      class="w-full flex items-center justify-between px-4 py-3 transition-colors bg-default-50 hover:bg-default-100 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-t-md"
      @click="$emit('toggle')"
    >
      <div class="flex items-center gap-2">
        <span class="font-medium text-default-800 dark:text-default-100">{{ title }}</span>
        <span v-if="count !== undefined" class="text-xs text-default-500 dark:text-default-400">({{ count }})</span>
      </div>
      <i :class="['bx', open ? 'bx-chevron-up' : 'bx-chevron-down', 'text-default-500 dark:text-default-400']"></i>
    </button>
    <div v-show="open" class="px-4 pb-4 bg-transparent dark:bg-transparent rounded-b-md">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: { type: String, required: true },
    count: { type: Number, default: undefined },
    open: { type: Boolean, default: false }
  },
  emits: ['toggle']
}
</script>

<style scoped>
</style>


