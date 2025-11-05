<template>
  <AppLayout :title="'Modelos de Documentos'" :description="'Gerencie templates por tipo'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="p-6 bg-default-50 dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-default-900">Modelos de Documentos</h2>
            <div class="flex gap-2">
              <a href="/document-types" class="ti-btn btn-wave ti-btn-secondary-full !py-2 !px-3">
                <i class="ri-settings-3-line mr-1"></i>Tipos de Documentos
              </a>
              <button @click="openCreateTab(defaultType)" :disabled="!defaultType" class="ti-btn btn-wave ti-btn-primary-full !py-2 !px-3 disabled:opacity-50">Novo Modelo</button>
            </div>
          </div>

          <div v-for="group in grouped" :key="group.type" class="mb-3">
            <Accordion :title="group.typeData.name" :count="group.items.length" :open="isOpen(group.type)" @toggle="toggle(group.type)">
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
                        <button 
                          class="ti-btn ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem] hover:bg-primary hover:text-white transition-colors hs-tooltip" 
                          @click="openEditTab(item)"
                          data-hs-tooltip-content="Editar"
                        >
                          <i class="ri-edit-line"></i>
                        </button>
                        <button 
                          class="ti-btn ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem] hover:bg-secondary hover:text-white transition-colors hs-tooltip" 
                          @click="duplicate(item)"
                          data-hs-tooltip-content="Duplicar"
                        >
                          <i class="ri-file-copy-line"></i>
                        </button>
                        <button 
                          class="ti-btn ti-btn-outline-warning !py-1 !px-2 !text-[0.75rem] hover:bg-warning hover:text-white transition-colors hs-tooltip" 
                          @click="setDefault(item)"
                          data-hs-tooltip-content="Definir como padrão"
                        >
                          <i class="ri-star-line"></i>
                        </button>
                        <button 
                          class="ti-btn ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem] hover:bg-danger hover:text-white transition-colors hs-tooltip" 
                          @click="remove(item)"
                          data-hs-tooltip-content="Remover"
                        >
                          <i class="ri-delete-bin-line"></i>
                        </button>
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
import Accordion from '@/Components/Accordion.vue'
import { useToast } from '@/composables/useToast'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTabsStore } from '@/stores/useTabsStore'

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
      typesData: [],
      itemsByType: {},
      openTypes: {},
      selectedIds: [],
      defaultType: null,
    }
  },
  computed: {
    grouped() {
      return this.types.map(code => {
        const typeData = this.typesData.find(t => t.code === code)
        return {
          type: code,
          typeData: typeData || { name: code, code },
          items: this.itemsByType[code] || []
        }
      })
    }
  },
  created() {
    this.fetchTypes()
  },
  methods: {
    labelOf(t) {
      const typeData = this.typesData.find(type => type.code === t)
      return typeData ? typeData.name : t
    },
    async fetchTypes() {
      try {
        const { data: typesCodes } = await window.axios.get('/api/document-templates/types')
        this.types = typesCodes

        const { data: typesFull } = await window.axios.get('/api/document-types')
        this.typesData = typesFull

        if (this.types.length > 0 && !this.defaultType) {
          const firstType = this.typesData.find(t => t.code === this.types[0]) || { code: this.types[0], name: this.types[0] }
          this.defaultType = { value: firstType.code, name: firstType.name }
        }

        await Promise.all(this.types.map(t => this.fetchByType(t)))
        this.types.forEach(t => {
          this.openTypes[t] = true
        })
      } catch (error) {
        console.error('Erro ao buscar tipos:', error)
        this.types = []
        this.typesData = []
      }
    },
    async fetchByType(type) {
      try {
        const { data } = await window.axios.get('/api/document-templates', { params: { type, per_page: 100 } })
        const items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
        this.itemsByType[type] = items
      } catch (error) {
        console.error(`Erro ao buscar templates do tipo ${type}:`, error)
        this.itemsByType[type] = []
      }
    },
    isOpen(type) { return !!this.openTypes[type] },
    toggle(type) { this.openTypes[type] = !this.openTypes[type] },
    allSelected(items) { return items.length && items.every(i => this.selectedIds.includes(i.id)) },
    toggleAll(items, checked) {
      const ids = items.map(i => i.id)
      this.selectedIds = checked ? Array.from(new Set([...this.selectedIds, ...ids])) : this.selectedIds.filter(id => !ids.includes(id))
    },
    openCreateTab(type) {
      if (!type) {
        this.toast.error('Aguarde o carregamento dos tipos de documentos')
        return
      }
      
      const tempKey = `new-${Date.now()}`
      const path = `/document-templates/new/${tempKey}`
      const ok = this.tabsStore.addTab({
        key: tempKey,
        title: `Novo Modelo (${type.name})`,
        mode: 'create',
        componentName: 'DocumentTemplatesForm',
        path,
        props: { mode: 'create', tempKey, type: type.value },
        context: 'document-templates'
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
        context: 'document-templates'
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


