<template>
  <AppLayout :title="'Modelos de Documentos'" :description="'Gerencie templates por tipo'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="p-6 bg-default-50 dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-default-900">Modelos de Documentos</h2>
            <div class="flex gap-2">
              <button @click="openCreateTab(defaultType)" class="ti-btn btn-wave ti-btn-primary-full !py-2 !px-3">Novo Modelo</button>
              <button :disabled="!selectedIds.length" @click="bulkDelete" class="ti-btn btn-wave ti-btn-danger-full disabled:opacity-50 !py-2 !px-3">Remover Selecionados</button>
            </div>
          </div>

          <div v-for="group in grouped" :key="group.type" class="mb-3">
            <Accordion :title="labelOf(group.type)" :count="group.items.length" :open="isOpen(group.type)" @toggle="toggle(group.type)">
              <div class="overflow-x-auto">
                <table class="min-w-full text-sm table-auto bg-transparent dark:bg-bgdark">
                  <thead class="bg-transparent dark:bg-transparent">
                    <tr class="text-left text-default-600 dark:text-default-300 border-b border-default-200 dark:border-white/10">
                      <th class="w-8"><input type="checkbox" :checked="allSelected(group.items)" @change="toggleAll(group.items, $event.target.checked)" /></th>
                      <th class="bg-transparent dark:bg-transparent">Nome</th>
                      <th class="bg-transparent dark:bg-transparent">Idioma</th>
                      <th class="bg-transparent dark:bg-transparent">País</th>
                      <th class="bg-transparent dark:bg-transparent">Status</th>
                      <th class="bg-transparent dark:bg-transparent">Sistema</th>
                      <th class="bg-transparent dark:bg-transparent">Padrão</th>
                      <th class="text-right bg-transparent dark:bg-transparent">Ações</th>
                    </tr>
                  </thead>
                  <tbody class="bg-transparent dark:bg-transparent">
                    <tr v-for="item in group.items" :key="item.id" class="border-b border-default-200 dark:border-white/10 bg-transparent dark:bg-transparent">
                      <td class="w-8 bg-transparent dark:bg-transparent"><input type="checkbox" :value="item.id" v-model="selectedIds" /></td>
                      <td class="bg-transparent dark:bg-transparent">{{ item.name }}</td>
                      <td class="bg-transparent dark:bg-transparent">{{ item.language || '-' }}</td>
                      <td class="bg-transparent dark:bg-transparent">{{ item.country || '-' }}</td>
                      <td class="bg-transparent dark:bg-transparent">
                        <span :class="item.status === 'active' ? 'text-green-600' : 'text-gray-500'">{{ item.status }}</span>
                      </td>
                      <td class="bg-transparent dark:bg-transparent">{{ item.is_system ? 'Sim' : 'Não' }}</td>
                      <td class="bg-transparent dark:bg-transparent">
                        <span v-if="item.is_default" class="px-2 py-1 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 rounded">Padrão</span>
                      </td>
                      <td class="text-right space-x-2 bg-transparent dark:bg-transparent">
                        <button class="ti-btn ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem]" @click="openEditTab(item)">Editar</button>
                        <button class="ti-btn ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem]" @click="duplicate(item)">Duplicar</button>
                        <button class="ti-btn ti-btn-outline-warning !py-1 !px-2 !text-[0.75rem]" @click="setDefault(item)">Padrão</button>
                        <button class="ti-btn ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem]" @click="remove(item)">Remover</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </Accordion>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Accordion from '@/Components/Accordion.vue'
import { useTabsStore } from '@/stores/useTabsStore'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, Accordion },
  setup() {
    const tabsStore = useTabsStore()
    const toast = useToast()
    return { tabsStore, toast }
  },
  data() {
    return {
      user: this.$page.props.user || null,
      types: [],
      itemsByType: {},
      openTypes: {},
      selectedIds: [],
      defaultType: { value: 'contract', name: 'Contratos' },
    }
  },
  computed: {
    grouped() {
      return this.types.map(t => ({ type: t, items: this.itemsByType[t] || [] }))
    }
  },
  created() {
    this.fetchTypes()
  },
  methods: {
    labelOf(t) {
      if (t === 'contract') return 'Contratos'
      if (t === 'invoice') return 'Faturas'
      if (t === 'quote') return 'Orçamentos'
      return t
    },
    async fetchTypes() {
      const { data } = await window.axios.get('/api/document-templates/types')
      this.types = data
      await Promise.all(this.types.map(t => this.fetchByType(t)))
      this.types.forEach(t => this.$set(this.openTypes, t, true))
    },
    async fetchByType(type) {
      try {
        const { data } = await window.axios.get('/api/document-templates', { params: { type, per_page: 100 } })
        const items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
        this.$set(this.itemsByType, type, items)
      } catch (error) {
        console.error(`Erro ao buscar templates do tipo ${type}:`, error)
        this.$set(this.itemsByType, type, [])
      }
    },
    isOpen(type) { return !!this.openTypes[type] },
    toggle(type) { this.$set(this.openTypes, type, !this.openTypes[type]) },
    allSelected(items) { return items.length && items.every(i => this.selectedIds.includes(i.id)) },
    toggleAll(items, checked) {
      const ids = items.map(i => i.id)
      this.selectedIds = checked ? Array.from(new Set([...this.selectedIds, ...ids])) : this.selectedIds.filter(id => !ids.includes(id))
    },
    openCreateTab(type) {
      const tempKey = `new-${Date.now()}`
      const path = `/document-templates/new/${tempKey}`
      const ok = this.tabsStore.addTab({
        key: tempKey,
        title: `Novo Modelo (${type.name})`,
        mode: 'create',
        componentName: 'DocumentTemplatesForm',
        path,
        props: { mode: 'create', tempKey, type: type.value },
      })
      if (!ok) return this.toast.error('Limite de abas atingido')
      this.$inertia.visit(path)
    },
    openEditTab(template) {
      const exists = this.tabsStore.tabs.find(t => t.key === template.id)
      if (exists) { this.tabsStore.setActive(exists); return this.$inertia.visit(exists.path || `/document-templates/${template.id}/edit`) }
      const ok = this.tabsStore.addTab({
        key: template.id,
        title: template.name,
        mode: 'edit',
        componentName: 'DocumentTemplatesForm',
        path: `/document-templates/${template.id}/edit`,
        props: { mode: 'edit', id: template.id },
      })
      if (!ok) return this.toast.error('Limite de abas atingido')
      this.$inertia.visit(`/document-templates/${template.id}/edit`)
    },
    async setDefault(item) { await window.axios.post(`/api/document-templates/${item.id}/set-default`); await this.fetchByType(item.type) },
    async remove(item) { await window.axios.delete(`/api/document-templates/${item.id}`); await this.fetchByType(item.type); this.selectedIds = this.selectedIds.filter(id => id !== item.id) },
    async duplicate(item) { const payload = { ...item }; delete payload.id; payload.name = `${item.name} (Cópia)`; payload.is_default = false; await window.axios.post('/api/document-templates', payload); await this.fetchByType(item.type) },
    async bulkDelete() { const ids = [...this.selectedIds]; for (const id of ids) { const it = this.grouped.flatMap(g => g.items).find(i => i.id === id); if (it) await window.axios.delete(`/api/document-templates/${id}`) } this.selectedIds = []; await Promise.all(this.types.map(t => this.fetchByType(t))) },
  }
}
</script>


