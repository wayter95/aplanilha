<template>
  <AppLayout v-if="standalone" :title="computedTitle || 'Novo Tipo de Projeto'" description="">
    <Form @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
      <div class="grid grid-cols-12 gap-6">
        <div class="xl:col-span-12 col-span-12">
          <div class="box">
            <div class="box-header">
              <h5 class="box-title">{{ mode === 'edit' ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto' }}</h5>
            </div>
            <div class="box-body">
              <div class="max-w-2xl space-y-4">
                <Input
                  name="title"
                  label="Título"
                  rules="required"
                  v-model="form.title"
                  placeholder="Ex: Desenvolvimento Web"
                />
                
                <div class="space-y-2">
                  <label class="ti-form-label">Cor</label>
                  <div class="flex items-center gap-3">
                    <input
                      type="color"
                      v-model="form.color"
                      class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer"
                    />
                    <Input
                      name="color"
                      v-model="form.color"
                      placeholder="#000000"
                      class="flex-1"
                    />
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <Switch
                    name="is_active"
                    label="Status Ativo"
                    v-model="isActive"
                  />
                  <span class="text-sm text-textmuted dark:text-textmuted">
                    {{ isActive ? 'Ativo' : 'Bloqueado' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <div class="flex justify-end gap-2">
                <button type="submit" class="ti-btn ti-btn-primary-full">
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
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">{{ mode === 'edit' ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto' }}</h5>
          </div>
          <div class="box-body">
            <div class="max-w-2xl space-y-4">
              <Input
                name="title"
                label="Título"
                rules="required"
                v-model="form.title"
                placeholder="Ex: Desenvolvimento Web"
              />
              
              <div class="space-y-2">
                <label class="ti-form-label">Cor</label>
                <div class="flex items-center gap-3">
                  <input
                    type="color"
                    v-model="form.color"
                    class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer"
                  />
                  <Input
                    name="color"
                    v-model="form.color"
                    placeholder="#000000"
                    class="flex-1"
                  />
                </div>
              </div>

              <div class="flex items-center gap-4">
                <Switch
                  name="is_active"
                  label="Status Ativo"
                  v-model="isActive"
                />
                <span class="text-sm text-textmuted dark:text-textmuted">
                  {{ isActive ? 'Ativo' : 'Bloqueado' }}
                </span>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <div class="flex justify-end gap-2">
              <button type="submit" class="ti-btn ti-btn-primary-full">
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
import AppLayout from '@/Layouts/AppLayout.vue'
import Input from '@/Components/Input.vue'
import Switch from '@/Components/Switch.vue'
import { useTabFormDataStore } from '@/stores/useTabFormDataStore'
import { useTabsStore } from '@/stores/useTabsStore'
import { Form as VeeForm } from 'vee-validate'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, Form: VeeForm, Input, Switch },
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
    
    // Se tem tempKey/id, tenta recuperar do localStorage
    if (tempKey) {
      const formDataStore = useTabFormDataStore()
      const stored = formDataStore.getFormData(tempKey)
      if (stored) {
        console.log('[Form] Data() - Recuperando dados:', stored)
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
  },
  watch: {
    isActive(newVal) {
      this.form.status = newVal ? 'a' : 'b'
    },
    form: {
      handler(newVal) {
        if (this.isInitializing) return
        const formDataStore = useTabFormDataStore()
        const tabKey = this.tabKey || this.tempKey || this.id
        if (tabKey) {
          const validFields = {
            title: newVal.title || '',
            color: newVal.color || '#000000',
            status: newVal.status || 'a',
          }
          console.log('[Form] Salvando dados no localStorage:', tabKey, validFields)
          formDataStore.setFormData(tabKey, validFields)
        }
      },
      deep: true,
      immediate: false
    },
  },
  mounted() {
    this.tabKey = this.tempKey || this.id
    
    console.log('[Form] Mounted - tabKey:', this.tabKey)
    console.log('[Form] Estado atual do form:', this.form)

    if (this.mode === 'edit' && this.id) {
      this.loadData()
    } else {
      this.isInitializing = false
    }
  },
  methods: {
    async loadData() {
      try {
        const response = await fetch(`/api/project-types/${this.id}`)
        const data = await response.json()
        
        if (data.success && data.data) {
          this.form.title = data.data.title
          this.form.color = data.data.color
          this.form.status = data.data.status
          this.isActive = data.data.status === 'a'
          
          const formDataStore = useTabFormDataStore()
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
      const tabsStore = useTabsStore()
      
      try {
        const url = this.mode === 'edit' 
          ? `/api/project-types/${this.id}`
          : '/api/project-types'
        
        const method = this.mode === 'edit' ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(this.form)
        })

        const data = await response.json()
        
        if (data.success) {
          toast.success(data.message)
          
          // Limpa os dados do formulário
          const formDataStore = useTabFormDataStore()
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
