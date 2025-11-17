<template>
  <AppLayout title="" description="">
    <Breadcrumb
      :title="pageTitle"
      :items="breadcrumbItems"
    />
    
    <Form @submit="handleSubmit" @invalid="handleInvalid" :initial-values="form" v-slot="{ errors }">
      <div class="grid grid-cols-12 gap-6">
        <div class="xl:col-span-12 col-span-12">
          <div class="box">
            <div class="box-header">
              <div class="box-title">
                Informações do Tipo de Projeto
              </div>
            </div>
            <div class="box-body">
              <div class="grid grid-cols-12 gap-4">
                <!-- Título -->
                <div class="xl:col-span-12 col-span-12">
                  <FormInput
                    name="title"
                    label="Título"
                    v-model="form.title"
                    type="text"
                    placeholder="Ex: Desenvolvimento Web"
                    rules="required"
                    required
                  />
                </div>

                <!-- Cor -->
                <div class="xl:col-span-6 col-span-12">
                  <ColorPicker
                    v-model="form.color"
                    label="Cor"
                    hint="Escolha uma cor para identificar este tipo de projeto"
                    :show-presets="true"
                  />
                </div>

                <!-- Status -->
                <div class="xl:col-span-6 col-span-12">
                  <Switch
                    name="status"
                    label="Status"
                    v-model="isActive"
                    size="sm"
                    active-label="Ativo"
                    inactive-label="Bloqueado"
                    :show-inline-label="true"
                    help-text="Define se o tipo de projeto está disponível para uso"
                  />
                </div>
              </div>
            </div>
            <div class="box-footer">
              <div class="flex items-center justify-end gap-2">
                <Button
                  variant="light"
                  style-type="outline"
                  size="sm"
                  left-icon="ri-close-line"
                  @click="handleCancel"
                >
                  Cancelar
                </Button>
                <Button
                  type="submit"
                  variant="primary"
                  size="sm"
                  left-icon="ri-save-line"
                >
                  {{ mode === 'edit' ? 'Atualizar' : 'Salvar' }}
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Form>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import { Form } from 'vee-validate'
import FormInput from '@/Components/Form/FormInput.vue'
import Button from '@/Components/Button.vue'
import ColorPicker from '@/Components/Common/ColorPicker.vue'
import Switch from '@/Components/Switch.vue'
import { useToast } from '@/composables/useToast'
import projectTypesService from '@/api/projectTypesService'

export default {
  components: { AppLayout, Breadcrumb, Form, FormInput, Button, ColorPicker, Switch },
  
  props: {
    mode: { type: String, default: 'create' },
    id: { type: String, default: null },
    projectType: { type: Object, default: null }
  },
  
  data() {
    const initialForm = this.initializeForm()
    
    return {
      form: initialForm,
      isActive: initialForm.status === 'a'
    }
  },
  
  computed: {
    pageTitle() {
      return this.mode === 'edit' ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto'
    },
    
    breadcrumbItems() {
      return [
        { label: 'Início', href: '/' },
        { label: 'Tipos de Projetos', href: '/project-types' },
        { label: this.mode === 'edit' ? 'Editar' : 'Novo' }
      ]
    },
    
    isEditMode() {
      return this.mode === 'edit'
    }
  },
  
  watch: {
    isActive(value) {
      this.form.status = value ? 'a' : 'b'
    }
  },
  
  methods: {
    initializeForm() {
      if (this.projectType) {
        return this.mapProjectTypeToForm(this.projectType)
      }
      
      return this.getDefaultForm()
    },
    
    getDefaultForm() {
      return {
        title: '',
        color: '#000000',
        status: 'a'
      }
    },
    
    mapProjectTypeToForm(projectType) {
      return {
        title: projectType.title || '',
        color: projectType.color || '#000000',
        status: projectType.status || 'a'
      }
    },
    
    async handleSubmit() {
      this.isEditMode ? await this.updateRecord() : await this.createRecord()
    },
    
    async createRecord() {
      try {
        const { success, message } = await projectTypesService.create(this.form)
        
        if (success) {
          useToast().success(message)
          this.afterSave()
        } else {
          useToast().error(message || 'Erro ao criar tipo de projeto')
        }
      } catch (error) {
        console.error('Erro ao criar:', error)
        useToast().error('Erro ao criar tipo de projeto')
      }
    },
    
    async updateRecord() {
      if (!this.id && !this.projectType?.id) {
        useToast().error('ID do registro não encontrado')
        return
      }
      
      try {
        const recordId = this.id || this.projectType.id
        const { success, message } = await projectTypesService.update(recordId, this.form)
        
        if (success) {
          useToast().success(message)
          this.afterSave()
        } else {
          useToast().error(message || 'Erro ao atualizar tipo de projeto')
        }
      } catch (error) {
        console.error('Erro ao atualizar:', error)
        useToast().error('Erro ao atualizar tipo de projeto')
      }
    },
    
    afterSave() {
      this.$inertia.visit('/project-types')
    },
    
    handleInvalid() {
      useToast().error('Por favor, preencha todos os campos obrigatórios')
    },
    
    handleCancel() {
      this.$inertia.visit('/project-types')
    }
  }
}
</script>
