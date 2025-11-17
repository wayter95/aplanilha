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
                <label for="name" class="form-label">
                  Nome <span class="text-danger">*</span>
                </label>
                <Field
                  id="name"
                  name="name"
                  v-model="localForm.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="Ex: Contratos"
                  rules="required"
                />
                <ErrorMessage name="name" class="invalid-feedback" />
              </div>

              <!-- Código -->
              <div class="xl:col-span-6 col-span-12">
                <label for="code" class="form-label">
                  Código <span class="text-danger">*</span>
                </label>
                <Field
                  id="code"
                  name="code"
                  v-model="localForm.code"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.code }"
                  placeholder="Ex: contract"
                  rules="required"
                  :disabled="mode === 'edit'"
                />
                <ErrorMessage name="code" class="invalid-feedback" />
              </div>

              <!-- Ordem -->
              <div class="xl:col-span-6 col-span-12">
                <label for="sort_order" class="form-label">
                  Ordem
                </label>
                <Field
                  id="sort_order"
                  name="sort_order"
                  v-model.number="localForm.sort_order"
                  type="number"
                  class="form-control"
                  placeholder="0"
                />
              </div>

              <!-- Descrição -->
              <div class="xl:col-span-12 col-span-12">
                <label for="description" class="form-label">
                  Descrição
                </label>
                <Field
                  id="description"
                  name="description"
                  v-model="localForm.description"
                  as="textarea"
                  rows="3"
                  class="form-control"
                  placeholder="Descrição do tipo de documento"
                />
              </div>

              <!-- Status -->
              <div class="xl:col-span-12 col-span-12">
                <Switch
                  name="is_active"
                  label="Status"
                  v-model="localIsActive"
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
              <button
                type="button"
                class="ti-btn ti-btn-light"
                @click="handleCancel"
              >
                <i class="ri-close-line mr-1"></i>
                Cancelar
              </button>
              <button
                type="submit"
                class="ti-btn ti-btn-primary-full"
              >
                <i class="ri-save-line mr-1"></i>
                {{ mode === 'edit' ? 'Atualizar' : 'Salvar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Form>
</template>

<script setup>
import { Form, Field, ErrorMessage } from 'vee-validate'
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ColorPicker from '@/Components/Common/ColorPicker.vue'
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


