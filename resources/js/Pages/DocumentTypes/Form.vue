<template>
  <Form @submit="handleSubmit" @invalid="handleInvalid" :initial-values="form" :key="formKey" v-slot="{ errors }">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box">
          <div class="box-header">
            <div class="box-title">
              Básico
            </div>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-12 gap-4">
              <!-- Nome -->
              <div class="xl:col-span-12 col-span-12">
                <FormInput
                  name="name"
                  label="Nome"
                  v-model="localForm.name"
                  placeholder="Ex: Contratos"
                  rules="required"
                  required
                  :error-message="backendErrors?.name?.[0]"
                />
              </div>

              <!-- Código -->
              <div class="xl:col-span-6 col-span-12">
                <FormInput
                  name="code"
                  label="Código"
                  v-model="localForm.code"
                  placeholder="Ex: contract"
                  rules="required"
                  required
                  :disabled="mode === 'edit'"
                  :error-message="backendErrors?.code?.[0]"
                />
              </div>

              <!-- Ordem -->
              <div class="xl:col-span-6 col-span-12">
                <FormInput
                  name="sort_order"
                  label="Ordem"
                  v-model="localForm.sort_order"
                  type="number"
                  placeholder="0"
                  :error-message="backendErrors?.sort_order?.[0]"
                />
              </div>

              <!-- Descrição -->
              <div class="xl:col-span-12 col-span-12">
                <FormTextarea
                  name="description"
                  label="Descrição"
                  v-model="localForm.description"
                  rows="3"
                  placeholder="Descrição do tipo de documento"
                  :error-message="backendErrors?.description?.[0]"
                />
              </div>

              <!-- Status -->
              <div class="xl:col-span-12 col-span-12">
                <Switch
                  name="is_active"
                  label="Status"
                  v-model="localIsActive"
                  size="sm"
                  active-label="Ativo"
                  inactive-label="Inativo"
                  :show-inline-label="true"
                  help-text="Define se o tipo de documento está disponível para uso"
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
</template>

<script setup>
import { Form } from 'vee-validate'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import FormInput from '@/Components/Form/FormInput.vue'
import FormTextarea from '@/Components/Form/FormTextarea.vue'
import Button from '@/Components/Button.vue'
import Switch from '@/Components/Switch.vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  isActive: {
    type: Boolean,
    default: true
  },
  mode: {
    type: String,
    default: 'create'
  },
  formKey: {
    type: [String, Number],
    default: 0
  },
  backendErrors: {
    type: Object,
    default: () => ({})
  }
})

console.log('[FormContent] Props recebidas:', {
  form: props.form,
  isActive: props.isActive,
  mode: props.mode,
  formKey: props.formKey
})

const emit = defineEmits(['update:form', 'update:isActive', 'submit', 'invalid'])

// Estado local reativo
const localForm = ref({ ...props.form })
const localIsActive = ref(props.isActive)

console.log('[FormContent] Estado local inicializado:', {
  localForm: localForm.value,
  localIsActive: localIsActive.value
})

// Sincroniza mudanças locais com o componente pai
watch(localForm, (newVal) => {
  console.log('[FormContent] localForm mudou:', newVal)
  emit('update:form', newVal)
}, { deep: true })

watch(localIsActive, (newVal) => {
  console.log('[FormContent] localIsActive mudou:', newVal)
  emit('update:isActive', newVal)
  localForm.value.is_active = newVal
})

// Watch props changes
watch(() => props.form, (newVal) => {
  console.log('[FormContent] props.form mudou (vindo do pai):', newVal)
  localForm.value = { ...newVal }
}, { deep: true })

watch(() => props.isActive, (newVal) => {
  console.log('[FormContent] props.isActive mudou (vindo do pai):', newVal)
  localIsActive.value = newVal
})

watch(() => props.mode, (newVal) => {
  console.log('[FormContent] props.mode mudou:', newVal)
})

const handleSubmit = () => {
  emit('submit')
}

const handleInvalid = () => {
  emit('invalid')
}

const handleCancel = () => {
  router.visit('/document-types')
}
</script>


