<template>
  <AppLayout v-if="standalone" :title="computedTitle || 'Novo Modelo'" :description="''" :user="user">
  <Form @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
    <div class="overflow-hidden">
      <!-- Conteúdo do formulário -->
      <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Card esquerdo: Informações básicas e marca d'água (30%) -->
          <div class="w-full md:w-[30%] flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Informações</h3>
            
            <div class="space-y-4">
              <Select name="type" label="Tipo" :options="typeOptions" v-model="form.type" />
              <Input name="name" label="Nome" rules="required" v-model="form.name" />
              <Switch 
                name="is_default" 
                label="Definir como padrão" 
                v-model="form.is_default"
              />
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Marca d'água</h4>
              <ImageUpload
                v-model="form.watermark_image_key"
                folder="document-templates/watermarks"
                alt-text="Marca d'água"
              />
            </div>
          </div>

          <!-- Card direito: Conteúdo HTML (70%) -->
          <div class="flex-1 w-full md:w-[70%] bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Conteúdo HTML</h3>
            <div class="space-y-4">
              <Textarea 
                name="header_html" 
                label="Cabeçalho (HTML)" 
                :rows="3"
                placeholder="<h1>Título</h1>"
                v-model="form.header_html"
              />
              <Textarea 
                name="content_html" 
                label="Conteúdo (HTML)" 
                :rows="8"
                rules="required"
                placeholder="<p>Olá ${name}, ${current_date}</p>"
                v-model="form.content_html"
              />
              <Textarea 
                name="footer_html" 
                label="Rodapé (HTML)" 
                :rows="3"
                v-model="form.footer_html"
              />
            </div>
            
            <!-- Botão Salvar dentro do card -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
              <button type="submit" class="px-6 h-10 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
                <i class="ri-save-line mr-2"></i>Salvar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Form>
  </AppLayout>
  <Form v-else @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
    <div class="overflow-hidden">
      <!-- Conteúdo do formulário -->
      <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Card esquerdo: Informações básicas e marca d'água (30%) -->
          <div class="w-full md:w-[30%] flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Informações</h3>
            
            <div class="space-y-4">
              <Select name="type" label="Tipo" :options="typeOptions" v-model="form.type" />
              <Input name="name" label="Nome" rules="required" v-model="form.name" />
              <Switch 
                name="is_default" 
                label="Definir como padrão" 
                v-model="form.is_default"
              />
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Marca d'água</h4>
              <ImageUpload
                v-model="form.watermark_image_key"
                folder="document-templates/watermarks"
                alt-text="Marca d'água"
              />
            </div>
          </div>

          <!-- Card direito: Conteúdo HTML (70%) -->
          <div class="flex-1 w-full md:w-[70%] bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Conteúdo HTML</h3>
            <div class="space-y-4">
              <Textarea 
                name="header_html" 
                label="Cabeçalho (HTML)" 
                :rows="3"
                placeholder="<h1>Título</h1>"
                v-model="form.header_html"
              />
              <Textarea 
                name="content_html" 
                label="Conteúdo (HTML)" 
                :rows="8"
                rules="required"
                placeholder="<p>Olá ${name}, ${current_date}</p>"
                v-model="form.content_html"
              />
              <Textarea 
                name="footer_html" 
                label="Rodapé (HTML)" 
                :rows="3"
                v-model="form.footer_html"
              />
            </div>
            
            <!-- Botão Salvar dentro do card -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
              <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
                <i class="ri-save-line mr-2"></i>Salvar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Form>
</template>

<script>
import ImageUpload from '@/Components/ImageUpload.vue'
import Input from '@/Components/Input.vue'
import Select from '@/Components/Select.vue'
import Switch from '@/Components/Switch.vue'
import Textarea from '@/Components/Textarea.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTabFormMemoryStore } from '@/stores/useTabFormMemoryStore'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'
import { usePage } from '@inertiajs/vue3'
import { storeToRefs } from 'pinia'
import { Form as VeeForm } from 'vee-validate'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, Form: VeeForm, Input, Select, Textarea, Switch, ImageUpload },
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    tempKey: { type: String, default: null },
    type: { type: String, default: 'contract' },
    standalone: { type: Boolean, default: true },
  },
  data() {
    // Garante que o tipo seja inicializado ANTES de criar o form
    const initialType = this.type || 'contract'
    return {
      user: this.$page?.props?.user || null,
      tabKey: null,
      isInitializing: true,
      formKey: 0,
      form: {
        type: initialType,
        name: '',
        language: '',
        country: '',
        status: 'active',
        is_default: false,
        header_html: '',
        content_html: '',
        footer_html: '',
        watermark_image_key: '',
      },
      typeOptions: [],
      statusOptions: [
        { value: 'active', label: 'Ativo' },
        { value: 'inactive', label: 'Inativo' },
      ],
    }
  },
  computed: {
    computedTitle() {
      if (this.mode === 'edit' && this.form.name) {
        return this.form.name
      }
      if (this.mode === 'create') {
        // Garante que sempre tenha um tipo válido
        const type = this.form.type || this.type || 'contract'
        const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
        // Converte para singular: Contratos -> Contrato, Faturas -> Fatura, Orçamentos -> Orçamento
        const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
        return `Novo ${singularLabel}`
      }
      return 'Editar Modelo'
    },
  },
  watch: {
    form: {
      handler(newVal) {
        if (this.isInitializing) return
        const formDataStore = useTabFormMemoryStore()
        const tabKey = this.tabKey || this.tempKey || this.id
        if (tabKey) {
          formDataStore.setFormData(tabKey, { ...newVal })
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
          Object.assign(this.form, stored)
        }
      }
    },
    tempKey(newKey) {
      if (newKey) {
        this.tabKey = newKey
        const formDataStore = useTabFormMemoryStore()
        const stored = formDataStore.getFormData(newKey)
        if (stored) {
          Object.assign(this.form, stored)
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
    // Watch para activeTab do store (exposto via setup)
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
    // Watch para URL para recarregar quando voltar para a tab
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
    
    await this.fetchTypeOptions()
    
    this.tabKey = this.tempKey || this.id
    const tabKey = this.tabKey
    
    if (!tabKey) {
      this.isInitializing = false
      return
    }
    
    const exists = tabsStore.tabs.find(t => t.key === tabKey)
    if (!exists && tabKey) {
      const path = this.id ? `/document-templates/${this.id}/edit` : `/document-templates/new/${this.tempKey}`
      const type = this.form.type || this.type || 'contract'
      const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
      const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
      const title = this.id ? 'Carregando…' : `Novo ${singularLabel}`
      
      tabsStore.addTab({
        key: tabKey,
        title,
        mode: this.id ? 'edit' : 'create',
        componentName: 'DocumentTemplatesForm',
        path,
        props: this.id ? { mode: 'edit', id: this.id } : { mode: 'create', tempKey: this.tempKey, type: this.type }
      })
    } else if (exists) {
      const type = this.form.type || this.type || 'contract'
      const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
      const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? `Novo ${singularLabel}` : exists.title)
      
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
    
    if (this.mode === 'edit' && this.id) {
      await this.load()
      this.isInitializing = false
      return
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
      Object.assign(this.form, stored)
    } else {
      formDataStore.initializeFormData(tabKey, { type: this.type || 'contract' })
      const initialized = formDataStore.getFormData(tabKey)
      if (initialized) {
        Object.assign(this.form, initialized)
      }
    }
    
    this.$nextTick(() => {
      this.isInitializing = false
      this.updateTabTitle()
    })
  },
  mounted() {
  },
  methods: {
    handleInvalid({ errors, values }) {
      const firstError = Object.values(errors)[0]
      if (firstError) {
        window?.alert?.(firstError)
      }
    },
    async fetchTypeOptions() {
      try {
        const { data } = await window.axios.get('/document-types')
        this.typeOptions = data.map(t => ({
          value: t.code,
          label: t.name
        }))
      } catch (error) {
        this.typeOptions = []
      }
    },
    updateTabTitle() {
      const tabsStore = useTabsMemoryStore()
      const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
      if (!tab) return
      
      const type = this.form.type || this.type || 'contract'
      const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
      const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? `Novo ${singularLabel}` : tab.title)
      
      if (tab.title !== newTitle) {
        tab.title = newTitle
        const activeTabKey = tabsStore.activeTab?.key || null
        try {
          localStorage.setItem('tabs-store', JSON.stringify({ tabs: tabsStore.tabs, activeTabKey }))
        } catch (e) {
          // Silently fail
        }
      }
    },
    loadFormDataIfNeeded(explicitTabKey) {
      if (this.isInitializing) {
        return
      }
      
      if (this.mode === 'edit' && this.id) {
        return
      }
      
      const tabKey = explicitTabKey || this.tabKey || this.tempKey || this.id
      if (!tabKey) {
        return
      }
      
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
      
      if (!stored) {
        return
      }
      
      const hasData = stored.name || stored.country || stored.language || stored.header_html || stored.content_html || stored.footer_html
      if (hasData) {
        this.isInitializing = true
        Object.assign(this.form, stored)
        this.$nextTick(() => {
          this.isInitializing = false
        })
      }
    },
    getDefaultForm() {
      return {
        type: this.type || 'contract',
        name: '',
        language: '',
        country: '',
        status: 'active',
        is_default: false,
        header_html: '',
        content_html: '',
        footer_html: '',
        watermark_image_key: '',
      }
    },
    async load() {
      const formDataStore = useTabFormMemoryStore()
      const tabsStore = useTabsMemoryStore()
      const { data } = await window.axios.get(`/document-templates/${this.id}`)
      Object.assign(this.form, data)
      formDataStore.setFormData(this.tabKey, data)
      // Força re-render do VeeForm para refletir os novos valores iniciais
      await this.$nextTick()
      this.formKey++
      
      // Atualiza o título da aba após carregar
      const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
      if (tab && data.name) {
        tab.title = data.name
      }
    },
    reset() {
      this.isInitializing = true
      const formDataStore = useTabFormMemoryStore()
      if (this.mode === 'create') {
        formDataStore.clearFormData(this.tabKey)
        const defaultForm = this.getDefaultForm()
        Object.assign(this.form, defaultForm)
      } else {
        this.load()
        return
      }
      this.$nextTick(() => {
        this.isInitializing = false
      })
    },
    async save(values) {
      const formDataStore = useTabFormMemoryStore()
      const tabsStore = useTabsMemoryStore()
      const formData = values || this.form
      
      if (!formData.name) {
        window?.alert?.('Informe o nome do modelo')
        return
      }
      if (!formData.content_html) {
        window?.alert?.('O conteúdo do modelo é obrigatório')
        return
      }
      try {
        if (this.mode === 'create') {
          const { data } = await window.axios.post('/document-templates', formData)
          
          this.toast.success('Modelo criado com sucesso!')
          
          if (formData.is_default && data?.id) {
            await window.axios.post(`/document-templates/${data.id}/set-default`)
          }
          
          if (this.tempKey && data?.id) {
            formDataStore.clearFormData(this.tempKey)
            
            const oldTab = tabsStore.tabs.find(t => t.key === this.tempKey)
            if (oldTab) {
              const tabIndex = tabsStore.tabs.indexOf(oldTab)
              if (tabIndex !== -1) {
                tabsStore.tabs.splice(tabIndex, 1)
              }
            }
            
            const newPath = `/document-templates/${data.id}/edit`
            const newTab = {
              key: data.id,
              title: formData.name,
              mode: 'edit',
              componentName: 'DocumentTemplatesForm',
              path: newPath,
              props: { mode: 'edit', id: data.id },
              context: 'document-templates'
            }
            
            tabsStore.addTab(newTab)
            this.tabKey = data.id
            
            try {
              localStorage.setItem('tabs-store', JSON.stringify({ tabs: tabsStore.tabs, activeTabKey: data.id }))
            } catch (e) {
              // Silently fail
            }
            
            this.$inertia.visit(newPath)
          }
        } else {
          await window.axios.put(`/document-templates/${this.id}`, formData)
          formDataStore.clearFormData(this.tabKey)
          
          this.toast.success('Modelo atualizado com sucesso!')
          
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
        const backendMsg = error?.response?.data?.message || error?.message || 'Erro ao salvar modelo'
        window?.alert?.(backendMsg)
        this.toast?.error?.(backendMsg)
      }
    },
  }
}
</script>


