<template>
  <AppLayout :title="'Modelos de Documentos'" :description="'Gerencie templates por tipo'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box custom-box">
          <div class="box-header">
            <div class="flex items-center justify-between flex-wrap gap-4">
              <div class="box-title">
                Modelos de Documentos
              </div>
              <div class="flex flex-wrap gap-2">
                <a href="/document-types" class="ti-btn btn-wave ti-btn-secondary-full !py-1 !px-2 !text-[0.75rem]">
                  <i class="ri-settings-3-line mr-1"></i>Tipos de Documentos
                </a>
                <button @click="openCreateTab(defaultType)" :disabled="!defaultType" class="ti-btn btn-wave ti-btn-primary-full !py-1 !px-2 !text-[0.75rem] disabled:opacity-50">
                  <i class="ri-add-line mr-1"></i>Novo Modelo
                </button>
              </div>
            </div>
          </div>
          
          <div class="box-body">

          <div v-for="group in grouped" :key="group.type">
            <Accordion :title="group.typeData.name" :count="group.items.length" :open="isOpen(group.type)" @toggle="toggle(group.type)">
              <div class="overflow-auto">
                <div class="ti-custom-table ti-striped-table ti-custom-table-hover">
                  <table class="table whitespace-nowrap min-w-full">
                    <thead>
                      <tr class="border-b border-defaultborder">
                        <th scope="col" class="text-start w-8">
                          <input
                            type="checkbox"
                            :checked="allSelected(group.items)"
                            @change="toggleAll(group.items, $event.target.checked)"
                            class="form-check-input"
                          >
                        </th>
                        <th scope="col" class="text-start">Nome</th>
                        <th scope="col" class="text-start">Idioma</th>
                        <th scope="col" class="text-start">País</th>
                        <th scope="col" class="text-start">Status</th>
                        <th scope="col" class="text-start">Sistema</th>
                        <th scope="col" class="text-start">Padrão</th>
                        <th scope="col" class="text-start">Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in group.items" :key="item.id" class="crm-contact">
                        <td class="text-start w-8">
                          <input
                            type="checkbox"
                            :value="item.id"
                            v-model="selectedIds"
                            class="form-check-input"
                          >
                        </td>
                        <td class="text-start">{{ item.name }}</td>
                        <td class="text-start text-textmuted dark:text-textmuted">{{ item.language || '-' }}</td>
                        <td class="text-start text-textmuted dark:text-textmuted">{{ item.country || '-' }}</td>
                        <td class="text-start">
                          <span :class="[
                            'badge',
                            item.status === 'active' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'
                          ]">
                            {{ item.status === 'active' ? 'Ativo' : 'Inativo' }}
                          </span>
                        </td>
                        <td class="text-start">
                          <span :class="[
                            'badge',
                            item.is_system ? 'bg-info/10 text-info' : 'bg-light text-defaulttextcolor'
                          ]">
                            {{ item.is_system ? 'Sim' : 'Não' }}
                          </span>
                        </td>
                        <td class="text-start">
                          <span v-if="item.is_default" class="badge bg-success/10 text-success">Padrão</span>
                          <span v-else class="text-textmuted dark:text-textmuted">-</span>
                        </td>
                        <td class="text-start">
                          <div class="flex items-center gap-2">
                            <button 
                              class="ti-btn btn-wave ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem] !m-0 hs-tooltip" 
                              @click="openEditTab(item)"
                              data-hs-tooltip-content="Editar"
                            >
                              <i class="ri-edit-line"></i>
                            </button>
                            <button 
                              class="ti-btn btn-wave ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem] !m-0 hs-tooltip" 
                              @click="duplicate(item)"
                              data-hs-tooltip-content="Duplicar"
                            >
                              <i class="ri-file-copy-line"></i>
                            </button>
                            <button 
                              class="ti-btn btn-wave ti-btn-outline-warning !py-1 !px-2 !text-[0.75rem] !m-0 hs-tooltip" 
                              @click="setDefault(item)"
                              data-hs-tooltip-content="Definir como padrão"
                            >
                              <i class="ri-star-line"></i>
                            </button>
                            <button 
                              class="ti-btn btn-wave ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem] !m-0 hs-tooltip" 
                              @click="remove(item)"
                              data-hs-tooltip-content="Remover"
                            >
                              <i class="ri-delete-bin-line"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <tr v-if="group.items.length === 0">
                        <td colspan="8" class="text-center py-8">
                          <div class="text-textmuted dark:text-textmuted">
                            <i class="ri-search-line text-4xl mb-2"></i>
                            <p>Nenhum modelo encontrado</p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </Accordion>
          </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import Accordion from '@/Components/Accordion.vue'
import { useToast } from '@/composables/useToast'
import { useTooltip } from '@/composables/useTooltip'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'

export default {
  components: { AppLayout, Accordion },
  setup() {
    const tabsStore = useTabsMemoryStore()
    const toast = useToast()
    // Inicializar tooltips automaticamente
    useTooltip()
    return { tabsStore, toast }
  },
  data() {
    return {
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
        const { data: typesCodes } = await window.axios.get('/document-templates/types')
        this.types = typesCodes

        const { data: typesFull } = await window.axios.get('/document-types')
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
        this.types = []
        this.typesData = []
      }
    },
    async fetchByType(type) {
      try {
        const { data } = await window.axios.get('/document-templates', { params: { type, per_page: 100 } })
        const items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
        this.itemsByType[type] = items
      } catch (error) {
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
    async setDefault(item) { await window.axios.post(`/document-templates/${item.id}/set-default`); await this.fetchByType(item.type) },
    async remove(item) { await window.axios.delete(`/document-templates/${item.id}`); await this.fetchByType(item.type); this.selectedIds = this.selectedIds.filter(id => id !== item.id) },
    async duplicate(item) { const payload = { ...item }; delete payload.id; payload.name = `${item.name} (Cópia)`; payload.is_default = false; await window.axios.post('/document-templates', payload); await this.fetchByType(item.type) },
    async bulkDelete() { const ids = [...this.selectedIds]; for (const id of ids) { const it = this.grouped.flatMap(g => g.items).find(i => i.id === id); if (it) await window.axios.delete(`/document-templates/${id}`) } this.selectedIds = []; await Promise.all(this.types.map(t => this.fetchByType(t))) },
  }
}
</script>


