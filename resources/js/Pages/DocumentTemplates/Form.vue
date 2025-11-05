<template>
  <AppLayout v-if="standalone" :title="computedTitle || 'Novo Modelo'" :description="''" :user="user">
  <Form @submit="save" :initial-values="form">
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
  <Form v-else @submit="save" :initial-values="form">
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
import { useTabFormDataStore } from '@/stores/useTabFormDataStore'
import { useTabsStore } from '@/stores/useTabsStore'
import { usePage } from '@inertiajs/vue3'
import { storeToRefs } from 'pinia'
import { Form as VeeForm } from 'vee-validate'

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
      typeOptions: [
        { value: 'contract', label: 'Contratos' },
        { value: 'invoice', label: 'Faturas' },
        { value: 'quote', label: 'Orçamentos' },
      ],
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
        const formDataStore = useTabFormDataStore()
        const tabKey = this.tabKey || this.tempKey || this.id
        if (tabKey) {
          // Debug: verifica se está salvando
          console.log('Salvando dados do formulário para tabKey:', tabKey, newVal)
          formDataStore.setFormData(tabKey, { ...newVal })
        } else {
          console.warn('Form.vue: tabKey não encontrado para salvar dados')
        }
      },
      deep: true,
      immediate: false
    },
    tabKey(newKey, oldKey) {
      if (newKey && newKey !== oldKey) {
        const formDataStore = useTabFormDataStore()
        const stored = formDataStore.getFormData(newKey)
        if (stored) {
          Object.assign(this.form, stored)
        }
      }
    },
    tempKey(newKey) {
      if (newKey) {
        this.tabKey = newKey
        const formDataStore = useTabFormDataStore()
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
        
        // Usa o tabKey da tab ativa, não os valores antigos das props
        const activeTabKey = newTab.key
        const currentTabKey = this.tabKey || this.tempKey || this.id
        
        // Só processa se a tab ativa corresponde à tab atual deste componente
        if (activeTabKey === currentTabKey && (!oldTab || oldTab.key !== activeTabKey)) {
          // Atualiza o tabKey do componente para garantir sincronização
          this.tabKey = activeTabKey
          
          // Tab foi ativada, força carregar dados
          console.log('activeTab watch: Tab foi ativada, carregando dados para tabKey:', activeTabKey)
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
      const tabsStore = useTabsStore()
      const currentPath = newUrl.split('?')[0]
      
      // Encontra a tab que corresponde à URL atual
      const matchingTab = tabsStore.tabs.find(t => {
        const tabPath = t.path.split('?')[0]
        return tabPath === currentPath
      })
      
      if (matchingTab) {
        // Atualiza o tabKey do componente para a tab encontrada
        this.tabKey = matchingTab.key
        
        // Força o carregamento dos dados quando volta para a tab
        console.log('page.url watch: URL corresponde à tab, carregando dados para tabKey:', matchingTab.key)
        this.$nextTick(() => {
          this.loadFormDataIfNeeded(matchingTab.key)
        })
      }
    }
  },
  setup() {
    const tabsStore = useTabsStore()
    const page = usePage()
    const { activeTab } = storeToRefs(tabsStore)
    return { activeTab, page }
  },
  async created() {
    this.isInitializing = true
    const tabsStore = useTabsStore()
    const formDataStore = useTabFormDataStore()
    
    this.tabKey = this.tempKey || this.id
    const tabKey = this.tabKey
    
    if (!tabKey) {
      this.isInitializing = false
      return
    }
    
    // Registra a aba automaticamente ao entrar via URL direta
    const exists = tabsStore.tabs.find(t => t.key === tabKey)
    if (!exists && tabKey) {
      const path = this.id ? `/document-templates/${this.id}/edit` : `/document-templates/new/${this.tempKey}`
      // Calcula o título usando o tipo garantido
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
      // Atualiza o título da tab existente se necessário
      // Força atualização do título usando o tipo garantido
      const type = this.form.type || this.type || 'contract'
      const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
      const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? `Novo ${singularLabel}` : exists.title)
      
      // Sempre atualiza o título para garantir que está correto
      exists.title = newTitle
      
      // Salva no storage
      const activeTabKey = tabsStore.activeTab?.key || null
      const saveToStorage = (tabs, activeTabKey) => {
        try {
          localStorage.setItem('tabs-store', JSON.stringify({ tabs, activeTabKey }))
        } catch (e) {
          console.warn('Erro ao salvar tabs no storage:', e)
        }
      }
      saveToStorage(tabsStore.tabs, activeTabKey)
    }
    
    // Modo edit: sempre carrega do servidor
    if (this.mode === 'edit' && this.id) {
      await this.load()
      this.isInitializing = false
      return
    }
    
    // Modo create: carrega dados salvos ou inicializa
    // Verifica diretamente no localStorage para evitar race conditions
    let stored = formDataStore.getFormData(tabKey)
    
    // Se não encontrou na store, tenta carregar do localStorage diretamente (para evitar race condition)
    if (!stored) {
      try {
        const storageData = JSON.parse(localStorage.getItem('tab-form-data-store') || '{}')
        if (storageData[tabKey]) {
          // Sincroniza a store com o localStorage
          formDataStore.setFormData(tabKey, storageData[tabKey])
          stored = formDataStore.getFormData(tabKey)
        }
      } catch (e) {
        console.warn('Erro ao ler localStorage:', e)
      }
    }
    
    if (stored) {
      // Dados salvos encontrados - sempre carrega, mesmo se vazio
      const hasStoredData = stored.name || stored.country || stored.language || stored.header_html || stored.content_html || stored.footer_html
      
      if (hasStoredData) {
        console.log('Form.vue created: Carregando dados salvos com conteúdo:', stored)
      } else {
        console.log('Form.vue created: Carregando estrutura existente (vazia)')
      }
      Object.assign(this.form, stored)
    } else {
      // Não há dados salvos - inicializa estrutura vazia
      console.log('Form.vue created: Não há dados salvos, inicializando estrutura vazia')
      formDataStore.initializeFormData(tabKey, { type: this.type || 'contract' })
      // Carrega a estrutura inicializada
      const initialized = formDataStore.getFormData(tabKey)
      if (initialized) {
        Object.assign(this.form, initialized)
      }
    }
    
    this.$nextTick(() => {
      this.isInitializing = false
      // Atualiza o título da tab após inicialização
      this.updateTabTitle()
    })
  },
  mounted() {
    // Os dados já foram carregados no created()
    // Este hook pode ser usado para ações que precisam do DOM
  },
  methods: {
    updateTabTitle() {
      const tabsStore = useTabsStore()
      const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
      if (!tab) return
      
      const type = this.form.type || this.type || 'contract'
      const typeLabel = this.typeOptions.find(o => o.value === type)?.label || type
      const singularLabel = typeLabel.endsWith('s') ? typeLabel.slice(0, -1) : typeLabel
      const newTitle = this.mode === 'edit' && this.form.name ? this.form.name : (this.mode === 'create' ? `Novo ${singularLabel}` : tab.title)
      
      if (tab.title !== newTitle) {
        tab.title = newTitle
        // Salva no storage
        const activeTabKey = tabsStore.activeTab?.key || null
        const saveToStorage = (tabs, activeTabKey) => {
          try {
            localStorage.setItem('tabs-store', JSON.stringify({ tabs, activeTabKey }))
          } catch (e) {
            console.warn('Erro ao salvar tabs no storage:', e)
          }
        }
        saveToStorage(tabsStore.tabs, activeTabKey)
      }
    },
    loadFormDataIfNeeded(explicitTabKey) {
      if (this.isInitializing) {
        console.log('loadFormDataIfNeeded: Ignorado porque está inicializando')
        return
      }
      
      // Não recarrega se já está em modo edit e tem ID (dados vêm do servidor)
      if (this.mode === 'edit' && this.id) {
        return
      }
      
      // Usa o tabKey explícito passado como parâmetro, ou tenta determinar do componente
      const tabKey = explicitTabKey || this.tabKey || this.tempKey || this.id
      if (!tabKey) {
        console.log('loadFormDataIfNeeded: tabKey não encontrado')
        return
      }
      
      // Atualiza o tabKey do componente se foi passado explicitamente
      if (explicitTabKey && explicitTabKey !== this.tabKey) {
        this.tabKey = explicitTabKey
      }
      
      const formDataStore = useTabFormDataStore()
      
      // Primeiro tenta na store
      let stored = formDataStore.getFormData(tabKey)
      
      // Se não encontrou, tenta diretamente no localStorage
      if (!stored) {
        try {
          const storageData = JSON.parse(localStorage.getItem('tab-form-data-store') || '{}')
          if (storageData[tabKey]) {
            // Sincroniza a store
            formDataStore.setFormData(tabKey, storageData[tabKey])
            stored = formDataStore.getFormData(tabKey)
          }
        } catch (e) {
          console.warn('Erro ao ler localStorage:', e)
        }
      }
      
      if (!stored) {
        console.log('loadFormDataIfNeeded: Não há dados salvos para tabKey:', tabKey)
        return
      }
      
      // Verifica se os dados têm conteúdo real antes de carregar
      const hasData = stored.name || stored.country || stored.language || stored.header_html || stored.content_html || stored.footer_html
      if (hasData) {
        console.log('loadFormDataIfNeeded: Carregando dados salvos com conteúdo para tabKey:', tabKey, stored)
        this.isInitializing = true
        Object.assign(this.form, stored)
        this.$nextTick(() => {
          this.isInitializing = false
          console.log('loadFormDataIfNeeded: Dados carregados para tabKey:', tabKey)
        })
      } else {
        console.log('loadFormDataIfNeeded: Dados salvos estão vazios para tabKey:', tabKey)
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
      const formDataStore = useTabFormDataStore()
      const tabsStore = useTabsStore()
      const { data } = await window.axios.get(`/api/document-templates/${this.id}`)
      Object.assign(this.form, data)
      formDataStore.setFormData(this.tabKey, data)
      
      // Atualiza o título da aba após carregar
      const tab = tabsStore.tabs.find(t => t.key === this.tabKey)
      if (tab && data.name) {
        tab.title = data.name
      }
    },
    reset() {
      this.isInitializing = true
      const formDataStore = useTabFormDataStore()
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
      const formDataStore = useTabFormDataStore()
      const tabsStore = useTabsStore()
      const formData = values || this.form
      if (!formData.name) {
        console.warn('Salvar: nome é obrigatório')
        window?.alert?.('Informe o nome do modelo')
        return
      }
      if (!formData.content_html) {
        console.warn('Salvar: conteúdo é obrigatório (content_html)')
        window?.alert?.('O conteúdo do modelo é obrigatório')
        return
      }
      try {
        if (this.mode === 'create') {
          const { data } = await window.axios.post('/api/document-templates', formData)
          if (formData.is_default && data?.id) {
            await window.axios.post(`/api/document-templates/${data.id}/set-default`)
          }
          if (this.tempKey && data?.id) {
            formDataStore.clearFormData(this.tempKey)
            tabsStore.convertToEdit(this.tempKey, data.id, formData.name)
            this.tabKey = data.id
            this.$inertia.visit(`/document-templates/${data.id}/edit`)
          }
        } else {
          await window.axios.put(`/api/document-templates/${this.id}`, formData)
          formDataStore.clearFormData(this.tabKey)
        }
      } catch (error) {
        console.error('Erro ao salvar modelo:', error)
        const backendMsg = error?.response?.data?.message || 'Erro ao salvar modelo'
        window?.alert?.(backendMsg)
        // opcional: exibir notificação se houver sistema de toasts
        // this.toast?.error?.(backendMsg)
      }
    },
  }
}
</script>


