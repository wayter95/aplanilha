<template>
  <AppLayout :title="computedTitle || 'Novo Modelo'" :description="''" :user="user">
    <Form @submit="save" @invalid="handleInvalid" :initial-values="form" :key="formKey">
      <div class="overflow-hidden">
        <div class="p-6">
          <div class="flex flex-col md:flex-row gap-6">
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
  </AppLayout>
</template>

<script>
import ImageUpload from '@/Components/ImageUpload.vue'
import Input from '@/Components/Input.vue'
import Select from '@/Components/Select.vue'
import Switch from '@/Components/Switch.vue'
import Textarea from '@/Components/Textarea.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { Form as VeeForm } from 'vee-validate'
import { useToast } from '@/composables/useToast'

export default {
  components: { AppLayout, Form: VeeForm, Input, Select, Textarea, Switch, ImageUpload },
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    type: { type: String, default: 'contract' }
  },
  data() {
    const initialType = this.type || 'contract'
    return {
      user: this.$page?.props?.user || null,
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
    id(newId) {
      if (newId && this.mode === 'edit') {
        this.load()
      }
    },
  },
  setup() {
    const page = usePage()
    const toast = useToast()
    return { page, toast }
  },
  async created() {
    this.isInitializing = true
    
    await this.fetchTypeOptions()
    
    if (this.mode === 'edit' && this.id) {
      await this.load()
    }
    
    this.isInitializing = false
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
      const { data } = await window.axios.get(`/document-templates/${this.id}`)
      Object.assign(this.form, data)
      await this.$nextTick()
      this.formKey++
    },
    reset() {
      this.isInitializing = true
      if (this.mode === 'create') {
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
          
          this.$inertia.visit('/document-templates')
        } else {
          await window.axios.put(`/document-templates/${this.id}`, formData)
          this.toast.success('Modelo atualizado com sucesso!')
          this.$inertia.visit('/document-templates')
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


