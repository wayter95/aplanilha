<template>
  <AppLayout v-if="standalone" :title="computedTitle" :description="''" :user="user">
  <Form @submit="save" :initial-values="form">
    <div class="border rounded-lg p-4 bg-white dark:bg-gray-800 border-default-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-4">
        <div class="text-lg font-semibold dark:text-default-100">{{ computedTitle }}</div>
        <div class="flex gap-2">
          <button type="button" class="ti-btn ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem]" @click="reset">Reset</button>
          <button type="submit" class="ti-btn ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]">Salvar</button>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-6">
      <div class="grid grid-cols-2 gap-4">
        <BaseSelect name="type" label="Tipo" :options="typeOptions" v-model="form.type" />
        <BaseInput name="name" label="Nome" rules="required" v-model="form.name" />
        <BaseInput name="language" label="Idioma" v-model="form.language" />
        <BaseInput name="country" label="País" v-model="form.country" />
        <BaseSelect name="status" label="Status" :options="statusOptions" v-model="form.status" />
        <div class="flex items-end">
          <label class="flex items-center gap-2 text-sm dark:text-default-200"><input type="checkbox" v-model="form.is_default" /> Definir como padrão</label>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Plano de fundo (URL)</label>
        <input v-model="form.background_image_path" type="text" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://..." />
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-6">
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Cabeçalho (HTML)</label>
        <textarea v-model="form.header_html" rows="3" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="<h1>Título</h1>"></textarea>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Conteúdo (HTML)</label>
        <textarea v-model="form.content_html" rows="8" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="<p>Olá ${name}, ${current_date}</p>"></textarea>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Rodapé (HTML)</label>
        <textarea v-model="form.footer_html" rows="3" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
      </div>
    </div>
    </div>
  </Form>
  </AppLayout>
  <Form v-else @submit="save" :initial-values="form">
    <div class="border rounded-lg p-4 bg-white dark:bg-gray-800 border-default-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-4">
        <div class="text-lg font-semibold dark:text-default-100">{{ computedTitle }}</div>
        <div class="flex gap-2">
          <button type="button" class="ti-btn ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem]" @click="reset">Reset</button>
          <button type="submit" class="ti-btn ti-btn-primary-full !py-1 !px-2 !text-[0.75rem]">Salvar</button>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-6">
      <div class="grid grid-cols-2 gap-4">
        <BaseSelect name="type" label="Tipo" :options="typeOptions" v-model="form.type" />
        <BaseInput name="name" label="Nome" rules="required" v-model="form.name" />
        <BaseInput name="language" label="Idioma" v-model="form.language" />
        <BaseInput name="country" label="País" v-model="form.country" />
        <BaseSelect name="status" label="Status" :options="statusOptions" v-model="form.status" />
        <div class="flex items-end">
          <label class="flex items-center gap-2 text-sm dark:text-default-200"><input type="checkbox" v-model="form.is_default" /> Definir como padrão</label>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Plano de fundo (URL)</label>
        <input v-model="form.background_image_path" type="text" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://..." />
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-6">
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Cabeçalho (HTML)</label>
        <textarea v-model="form.header_html" rows="3" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="<h1>Título</h1>"></textarea>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Conteúdo (HTML)</label>
        <textarea v-model="form.content_html" rows="8" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="<p>Olá ${name}, ${current_date}</p>"></textarea>
      </div>
      <div>
        <label class="block text-sm mb-1 dark:text-default-200">Rodapé (HTML)</label>
        <textarea v-model="form.footer_html" rows="3" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
      </div>
    </div>
    </div>
  </Form>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Form as VeeForm } from 'vee-validate'
import BaseInput from '@/Components/Form/BaseInput.vue'
import BaseSelect from '@/Components/Form/BaseSelect.vue'
import { useTabsStore } from '@/stores/useTabsStore'
import { useTabFormDataStore } from '@/stores/useTabFormDataStore'
import { storeToRefs } from 'pinia'
import { usePage } from '@inertiajs/vue3'

export default {
  components: { AppLayout, Form: VeeForm, BaseInput, BaseSelect },
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    tempKey: { type: String, default: null },
    type: { type: String, default: 'contract' },
    standalone: { type: Boolean, default: true },
  },
  data() {
    return {
      user: this.$page?.props?.user || null,
      tabKey: null,
      isInitializing: true,
      form: {
        type: this.type || 'contract',
        name: '',
        language: '',
        country: '',
        status: 'active',
        is_default: false,
        header_html: '',
        content_html: '',
        footer_html: '',
        background_image_path: '',
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
      return this.mode === 'create' ? `Novo Modelo (${this.typeOptions.find(o => o.value === this.form.type)?.label || this.form.type})` : 'Editar Modelo'
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
      tabsStore.addTab({
        key: tabKey,
        title: this.id ? 'Carregando…' : `Novo Modelo (${this.typeOptions.find(o => o.value === this.form.type)?.label || this.form.type})`,
        mode: this.id ? 'edit' : 'create',
        componentName: 'DocumentTemplatesForm',
        path,
        props: this.id ? { mode: 'edit', id: this.id } : { mode: 'create', tempKey: this.tempKey, type: this.type }
      })
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
    })
  },
  mounted() {
    // Os dados já foram carregados no created()
    // Este hook pode ser usado para ações que precisam do DOM
  },
  methods: {
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
        background_image_path: '',
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


