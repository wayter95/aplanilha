<template>
  <AppLayout v-if="standalone" title="" description="">
    <!-- Breadcrumb -->
    <Breadcrumb
      :title="mode === 'edit' ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto'"
      :items="breadcrumbItems"
    />
    
    <!-- Form -->
    <FormContent 
      :form="form" 
      :isActive="isActive" 
      :mode="mode"
      :formKey="formKey"
      @update:form="form = $event"
      @update:isActive="isActive = $event"
      @submit="save"
      @invalid="handleInvalid"
    />
  </AppLayout>
  
  <!-- Versão sem AppLayout (para tabs) -->
  <FormContent 
    v-else
    :form="form" 
    :isActive="isActive" 
    :mode="mode"
    :formKey="formKey"
    @update:form="form = $event"
    @update:isActive="isActive = $event"
    @submit="save"
    @invalid="handleInvalid"
  />
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import FormContent from './FormContent.vue'
import { useTabFormMemoryStore } from '@/stores/useTabFormMemoryStore'
import { useTabsMemoryStore } from '@/stores/useTabsMemoryStore'
import { useToast } from '@/composables/useToast'
import projectTypesService from '@/api/projectTypesService'

export default {
  components: { AppLayout, Breadcrumb, FormContent },
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    tempKey: { type: String, default: null },
    standalone: { type: Boolean, default: true },
  },
  data() {
    // Tenta recuperar dados antes de inicializar
    const tempKey = this.tempKey || this.id
    let initialForm = {
      title: '',
      color: '#000000',
      status: 'a',
    }
    
    // Se tem tempKey/id, tenta recuperar do store em memória
    if (tempKey) {
      const formDataStore = useTabFormMemoryStore()
      const stored = formDataStore.getFormData(tempKey)
      if (stored) {
        initialForm = { ...initialForm, ...stored }
      }
    }
    
    return {
      tabKey: null,
      isInitializing: true,
      formKey: tempKey || 0,
      form: initialForm,
      isActive: initialForm.status === 'a',
    }
  },
  computed: {
    computedTitle() {
      if (this.mode === 'edit') {
        return this.form.title || 'Carregando…'
      }
      return 'Novo Tipo de Projeto'
    },
    breadcrumbItems() {
      return [
        { label: 'Projetos', href: '/projects' },
        { label: 'Tipos de Projetos', href: '/projects/types' },
        { label: this.mode === 'edit' ? 'Editar' : 'Novo' }
      ]
    }
  },
  watch: {
    isActive(newVal) {
      this.form.status = newVal ? 'a' : 'b'
    },
    form: {
      handler(newVal) {
        if (this.isInitializing) return
        
        // Salva dados em memória
        const formDataStore = useTabFormMemoryStore()
        const tabKey = this.tabKey || this.tempKey || this.id
        if (tabKey) {
          const validFields = {
            title: newVal.title || '',
            color: newVal.color || '#000000',
            status: newVal.status || 'a',
          }
          console.log('[Form] Salvando dados em memória:', tabKey, validFields)
          formDataStore.setFormData(tabKey, validFields)
          
          // Marca a tab como modificada
          const tabsStore = useTabsMemoryStore()
          tabsStore.markAsModified(tabKey)
        }
      },
      deep: true,
      immediate: false
    },
  },
  mounted() {
    this.tabKey = this.tempKey || this.id

    if (this.mode === 'edit' && this.id) {
      this.loadData()
    } else {
      this.isInitializing = false
    }
  },
  methods: {
    async loadData() {
      try {
        const data = await projectTypesService.get(this.id)
        
        if (data.success && data.data) {
          this.form.title = data.data.title
          this.form.color = data.data.color
          this.form.status = data.data.status
          this.isActive = data.data.status === 'a'
          
          const formDataStore = useTabFormMemoryStore()
          formDataStore.setFormData(this.tabKey, this.form)
        }
      } catch (error) {
        console.error('Erro ao carregar tipo de projeto:', error)
        useToast().error('Erro ao carregar dados')
      } finally {
        this.isInitializing = false
      }
    },
    async save() {
      const toast = useToast()
      const tabsStore = useTabsMemoryStore()
      
      try {
        // Usa o service ao invés de fetch direto
        const data = this.mode === 'edit'
          ? await projectTypesService.update(this.id, this.form)
          : await projectTypesService.create(this.form)
        
        if (data.success) {
          toast.success(data.message)
          
          // Marca a tab como limpa (não modificada)
          tabsStore.markAsClean(this.tabKey)
          
          // Limpa os dados do formulário
          const formDataStore = useTabFormMemoryStore()
          formDataStore.clearFormData(this.tabKey)
          
          // Fecha a tab se existir
          const currentTab = tabsStore.tabs.find(t => t.key === this.tabKey)
          if (currentTab) {
            await tabsStore.closeTab(currentTab)
          }
          
          this.$inertia.visit('/projects/types')
        } else {
          toast.error(data.message || 'Erro ao salvar tipo de projeto')
        }
      } catch (error) {
        console.error('Erro ao salvar:', error)
        toast.error('Erro ao salvar tipo de projeto')
      }
    },
    handleInvalid() {
      useToast().error('Por favor, preencha todos os campos obrigatórios')
    }
  }
}
</script>
