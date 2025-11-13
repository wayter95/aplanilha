<template>
  <AppLayout v-if="standalone" :title="computedTitle || 'Novo Tipo'" :description="''" :user="user">
    <Form @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
      <div class="overflow-hidden">
        <div class="p-6">
          <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Informações do Tipo</h3>
            
            <div class="space-y-4">
              <Input
                name="name"
                label="Nome"
                rules="required"
                v-model="form.name"
                placeholder="Ex: Contratos"
              />
              <Input
                name="code"
                label="Código"
                rules="required"
                v-model="form.code"
                placeholder="Ex: contract"
                :disabled="mode === 'edit'"
              />
              <Textarea
                name="description"
                label="Descrição"
                v-model="form.description"
                :rows="3"
                placeholder="Descrição do tipo de documento"
              />
              <div class="flex gap-4">
                <div class="flex-1">
                  <Input
                    name="sort_order"
                    label="Ordem"
                    type="number"
                    v-model="form.sort_order"
                  />
                </div>
                <div class="flex-1 flex items-center pt-6">
                  <Switch
                    name="is_active"
                    label="Ativo"
                    v-model="form.is_active"
                  />
                </div>
              </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
              <button type="submit" class="px-6 h-10 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
                <i class="ri-save-line mr-2"></i>Salvar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Form>
  </AppLayout>
  <Form v-else @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
    <div class="overflow-hidden">
      <div class="p-6">
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Informações do Tipo</h3>
          
          <div class="space-y-4">
            <Input
              name="name"
              label="Nome"
              rules="required"
              v-model="form.name"
              placeholder="Ex: Contratos"
            />
            <Input
              name="code"
              label="Código"
              rules="required"
              v-model="form.code"
              placeholder="Ex: contract"
              :disabled="mode === 'edit'"
            />
            <Textarea
              name="description"
              label="Descrição"
              v-model="form.description"
              :rows="3"
              placeholder="Descrição do tipo de documento"
            />
            <div class="flex gap-4">
              <div class="flex-1">
                <Input
                  name="sort_order"
                  label="Ordem"
                  type="number"
                  v-model="form.sort_order"
                />
              </div>
              <div class="flex-1 flex items-center pt-6">
                <Switch
                  name="is_active"
                  label="Ativo"
                  v-model="form.is_active"
                />
              </div>
            </div>
          </div>
          
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <button type="submit" class="px-6 h-10 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
              <i class="ri-save-line mr-2"></i>Salvar
            </button>
          </div>
        </div>
      </div>
    </div>
  </Form>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Input from '@/Components/Input.vue'
import Textarea from '@/Components/Textarea.vue'
import Switch from '@/Components/Switch.vue'
import { useTabFormMemoryStore } from '@/stores/useTabFormMemoryStore'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'
import { usePage } from '@inertiajs/vue3'
import { storeToRefs } from 'pinia'
import { Form as VeeForm } from 'vee-validate'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, Form: VeeForm, Input, Textarea, Switch },
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    tempKey: { type: String, default: null },
    standalone: { type: Boolean, default: true },
  },
  data() {
    return {
      user: this.$page?.props?.user || null,
      tabKey: null,
      isInitializing: true,
      formKey: 0,
      form: {
        name: '',
        code: '',
        description: '',
        is_active: true,
        sort_order: 0,
      },
    }
  },
  computed: {
    computedTitle() {
      if (this.mode === 'edit') {
        return this.form.name || 'Carregando…'
      }
      return 'Novo Tipo'
    },
  },
  watch: {
    form: {
      handler(newVal) {
        if (this.isInitializing) return
        const formDataStore = useTabFormMemoryStore()
        const tabKey = this.tabKey || this.tempKey || this.id
        if (tabKey) {
          const validFields = {
            name: newVal.name || '',
            code: newVal.code || '',
            description: newVal.description || '',
            is_active: newVal.is_active ?? true,
            sort_order: newVal.sort_order ?? 0,
          }
          formDataStore.setFormData(tabKey, validFields)
        }
      },
      deep: true,
      immediate: false
    },
    tabKey(newKey, oldKey) {
      if (newKey && newKey !== oldKey) {
        const formDataStore = useTabFormMemoryStore()
        const stored = formDataStore.getFormData(newKey)
        if (stored) {
          this.form.name = stored.name || ''
          this.form.code = stored.code || ''
          this.form.description = stored.description || ''
          this.form.is_active = stored.is_active ?? true
          this.form.sort_order = stored.sort_order ?? 0
        }
      }
    },
    tempKey(newKey) {
      if (newKey) {
        this.tabKey = newKey
        const formDataStore = useTabFormMemoryStore()
        const stored = formDataStore.getFormData(newKey)
        if (stored) {
          this.form.name = stored.name || ''
          this.form.code = stored.code || ''
          this.form.description = stored.description || ''
          this.form.is_active = stored.is_active ?? true
          this.form.sort_order = stored.sort_order ?? 0
        }
      }
    },
    id(newId) {
      if (newId) {
        this.tabKey = newId
        if (this.mode === 'edit') {
          this.load()
        }
      }
    },
    activeTab: {
      handler(newTab, oldTab) {
        if (!newTab) return
        const activeTabKey = newTab.key
        const currentTabKey = this.tabKey || this.tempKey || this.id
        if (activeTabKey === currentTabKey && (!oldTab || oldTab.key !== activeTabKey)) {
          this.tabKey = activeTabKey
          this.$nextTick(() => {
            this.loadFormDataIfNeeded(activeTabKey)
          })
        }
      },
      deep: true,
      immediate: false
    },
    'page.url'(newUrl) {
      const tabsStore = useTabsMemoryStore()
      const currentPath = newUrl.split('?')[0]
      const matchingTab = tabsStore.tabs.find(t => {
        const tabPath = t.path.split('?')[0]
        return tabPath === currentPath
      })
      if (matchingTab) {
        this.tabKey = matchingTab.key
        this.$nextTick(() => {
          this.loadFormDataIfNeeded(matchingTab.key)
        })
      }
    }
  },
  setup() {
    const tabsStore = useTabsMemoryStore()
    const page = usePage()
    const toast = useToast()
    const { activeTab } = storeToRefs(tabsStore)
    return { activeTab, page, toast }
  },
  async created() {
    this.isInitializing = true
    const tabsStore = useTabsMemoryStore()
    const formDataStore = useTabFormMemoryStore()
    
    this.tabKey = this.tempKey || this.id
    const tabKey = this.tabKey
    
    if (!tabKey) {
      this.isInitializing = false
      return
    }
    
    if (this.mode === 'edit' && this.id) {
      const exists = tabsStore.tabs.find(t => t.key === tabKey)
      if (!exists && tabKey) {
        const path = `/document-types/${this.id}/edit`
        tabsStore.addTab({
          key: tabKey,
          title: 'Carregando…',
          mode: 'edit',
          componentName: 'DocumentTypesForm',
          path,
          props: { mode: 'edit', id: this.id },
          context: 'document-types'
        })
      }
      
      await this.load()
      this.$nextTick(() => {
        this.updateTabTitle()
        this.isInitializing = false
      })
      return
    }
    
    const exists = tabsStore.tabs.find(t => t.key === tabKey)
    if (!exists && tabKey) {
      const path = `/document-types/new/${this.tempKey}`
      tabsStore.addTab({
        key: tabKey,
        title: 'Novo Tipo',
        mode: 'create',
        componentName: 'DocumentTypesForm',
        path,
        props: { mode: 'create', tempKey: this.tempKey },
        context: 'document-types'
      })
    } else if (exists) {
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? 'Novo Tipo' : exists.title)
      if (exists.title !== newTitle) {
        exists.title = newTitle
        const activeTabKey = tabsStore.activeTab?.key || null
        const saveToStorage = (tabs, activeTabKey) => {
          try {
            localStorage.setItem('tabs-store', JSON.stringify({ tabs, activeTabKey }))
          } catch (e) {
            // Silently fail
          }
        }
        saveToStorage(tabsStore.tabs, activeTabKey)
      }
    }
    
    let stored = formDataStore.getFormData(tabKey)
    
    if (!stored) {
      try {
        const storageData = JSON.parse(localStorage.getItem('tab-form-data-store') || '{}')
        if (storageData[tabKey]) {
          formDataStore.setFormData(tabKey, storageData[tabKey])
          stored = formDataStore.getFormData(tabKey)
        }
      } catch (e) {
        // Silently fail
      }
    }
    
    if (stored) {
      const hasStoredData = stored.name || stored.code || stored.description
      if (hasStoredData) {
        this.form.name = stored.name || ''
        this.form.code = stored.code || ''
        this.form.description = stored.description || ''
        this.form.is_active = stored.is_active ?? true
        this.form.sort_order = stored.sort_order ?? 0
      }
    } else {
      formDataStore.initializeFormData(tabKey, {
        name: '',
        code: '',
        description: '',
        is_active: true,
        sort_order: 0
      })
      const initialized = formDataStore.getFormData(tabKey)
      if (initialized) {
        this.form.name = initialized.name || ''
        this.form.code = initialized.code || ''
        this.form.description = initialized.description || ''
        this.form.is_active = initialized.is_active ?? true
        this.form.sort_order = initialized.sort_order ?? 0
      }
    }
    
    this.$nextTick(() => {
      this.isInitializing = false
      this.updateTabTitle()
    })
  },
  methods: {
    handleInvalid({ errors, values }) {
      const firstError = Object.values(errors)[0]
      if (firstError) {
        window?.alert?.(firstError)
      }
    },
    updateTabTitle() {
      const tabsStore = useTabsMemoryStore()
      const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
      if (!tab) return
      
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? 'Novo Tipo' : tab.title)
      
      if (tab.title !== newTitle) {
        tab.title = newTitle
        const activeTabKey = tabsStore.activeTab?.key || null
        const saveToStorage = (tabs, activeTabKey) => {
          try {
            localStorage.setItem('tabs-store', JSON.stringify({ tabs, activeTabKey }))
          } catch (e) {
            // Silently fail
          }
        }
        saveToStorage(tabsStore.tabs, activeTabKey)
      }
    },
    loadFormDataIfNeeded(explicitTabKey) {
      if (this.isInitializing) return
      if (this.mode === 'edit' && this.id) return
      
      const tabKey = explicitTabKey || this.tabKey || this.tempKey || this.id
      if (!tabKey) return
      
      if (explicitTabKey && explicitTabKey !== this.tabKey) {
        this.tabKey = explicitTabKey
      }
      
      const formDataStore = useTabFormMemoryStore()
      let stored = formDataStore.getFormData(tabKey)
      
      if (!stored) {
        try {
          const storageData = JSON.parse(localStorage.getItem('tab-form-data-store') || '{}')
          if (storageData[tabKey]) {
            formDataStore.setFormData(tabKey, storageData[tabKey])
            stored = formDataStore.getFormData(tabKey)
          }
        } catch (e) {
          // Silently fail
        }
      }
      
      if (!stored) return
      
      const hasData = stored.name || stored.code || stored.description
      if (hasData) {
        this.isInitializing = true
        this.form.name = stored.name || ''
        this.form.code = stored.code || ''
        this.form.description = stored.description || ''
        this.form.is_active = stored.is_active ?? true
        this.form.sort_order = stored.sort_order ?? 0
        this.$nextTick(() => {
          this.isInitializing = false
        })
      }
    },
    async load() {
      try {
        this.isInitializing = true
        const formDataStore = useTabFormMemoryStore()
        const tabsStore = useTabsMemoryStore()
        const { data } = await window.axios.get(`/api/document-types/${this.id}`)
        
        this.form.name = data.name || ''
        this.form.code = data.code || ''
        this.form.description = data.description || ''
        this.form.is_active = data.is_active ?? true
        this.form.sort_order = data.sort_order ?? 0
        
        const validData = {
          name: data.name || '',
          code: data.code || '',
          description: data.description || '',
          is_active: data.is_active ?? true,
          sort_order: data.sort_order ?? 0
        }
        
        formDataStore.setFormData(this.tabKey, validData)
        
        const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
        if (tab && data.name) {
          tab.title = data.name
          const activeTabKey = tabsStore.activeTab?.key || null
          const saveToStorage = (tabs, activeTabKey) => {
            try {
              localStorage.setItem('tabs-store', JSON.stringify({ tabs, activeTabKey }))
            } catch (e) {
              // Silently fail
            }
          }
          saveToStorage(tabsStore.tabs, activeTabKey)
        }
        
        await this.$nextTick()
        this.formKey++
        this.isInitializing = false
        this.updateTabTitle()
      } catch (error) {
        this.isInitializing = false
      }
    },
    async save(values) {
      const formDataStore = useTabFormMemoryStore()
      const tabsStore = useTabsMemoryStore()
      const rawData = values || this.form
      
      const formData = {
        name: rawData.name || '',
        code: rawData.code || '',
        description: rawData.description || '',
        is_active: rawData.is_active ?? true,
        sort_order: rawData.sort_order ?? 0
      }
      
      if (!formData.name) {
        window?.alert?.('Informe o nome do tipo')
        return
      }
      
      try {
        if (this.mode === 'create') {
          const { data } = await window.axios.post('/api/document-types', formData)
          this.toast.success('Tipo de documento criado com sucesso!')
          if (this.tempKey && data?.id) {
            formDataStore.clearFormData(this.tempKey)
            
            const oldTab = tabsStore.tabs.find(t => t.key === this.tempKey)
            if (oldTab) {
              tabsStore.closeTab(oldTab)
            }
            
            const newPath = `/document-types/${data.id}/edit`
            tabsStore.addTab({
              key: data.id,
              title: formData.name,
              mode: 'edit',
              componentName: 'DocumentTypesForm',
              path: newPath,
              props: { mode: 'edit', id: data.id },
              context: 'document-types'
            })
            
            this.tabKey = data.id
            this.$inertia.visit(newPath)
          }
        } else {
          await window.axios.put(`/api/document-types/${this.id}`, formData)
          formDataStore.clearFormData(this.tabKey)
          this.toast.success('Tipo de documento atualizado com sucesso!')
          
          const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
          if (tab && formData.name) {
            tab.title = formData.name
            const activeTabKey = tabsStore.activeTab?.key || null
            try {
              localStorage.setItem('tabs-store', JSON.stringify({ tabs: tabsStore.tabs, activeTabKey }))
            } catch (e) {
              // Silently fail
            }
          }
        }
      } catch (error) {
        const backendMsg = error?.response?.data?.message || error?.message || 'Erro ao salvar tipo'
        window?.alert?.(backendMsg)
      }
    },
  }
}
</script>

