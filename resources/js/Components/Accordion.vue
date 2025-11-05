<template>
  <div class="box custom-box mb-3">
    <div class="box-header">
      <button
        type="button"
        class="w-full flex items-center justify-between px-4 py-3 transition-colors hover:bg-light/50 dark:hover:bg-gray-700 rounded-t-md"
        @click="$emit('toggle')"
      >
        <div class="flex items-center gap-2">
          <span class="box-title font-medium">{{ title }}</span>
          <span v-if="count !== undefined" class="badge bg-primary/10 text-primary text-xs">({{ count }})</span>
        </div>
        <i :class="['ri', open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line', 'text-textmuted dark:text-textmuted text-lg']"></i>
      </button>
    </div>
    <div v-show="open" class="box-body !p-0">
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
.box {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.custom-box {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.box-header {
  background-color: rgb(var(--default-background));
  border-color: rgb(var(--bootstrap-card-border));
}

.box-title {
  color: rgb(var(--default-text-color));
}
</style>


