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
              <!-- Título -->
              <div class="xl:col-span-12 col-span-12">
                <label for="title" class="form-label">
                  Título <span class="text-danger">*</span>
                </label>
                <Field
                  id="title"
                  name="title"
                  v-model="localForm.title"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.title }"
                  placeholder="Ex: Desenvolvimento Web"
                  rules="required"
                />
                <ErrorMessage name="title" class="invalid-feedback" />
              </div>

              <!-- Cor -->
              <div class="xl:col-span-6 col-span-12">
                <ColorInput
                  id="color"
                  name="color"
                  label="Cor"
                  v-model="localForm.color"
                  placeholder="#000000"
                  rules="required"
                  :required="true"
                />
              </div>

              <!-- Status -->
              <div class="xl:col-span-6 col-span-12">
                <Switch
                  name="status"
                  label="Status"
                  v-model="localIsActive"
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
import ColorInput from '@/Components/ColorInput.vue'
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

const emit = defineEmits(['update:form', 'update:isActive', 'submit', 'invalid'])

// Estado local reativo
const localForm = ref({ ...props.form })
const localIsActive = ref(props.isActive)

// Sincroniza mudanças locais com o componente pai
watch(localForm, (newVal) => {
  emit('update:form', newVal)
}, { deep: true })

watch(localIsActive, (newVal) => {
  emit('update:isActive', newVal)
  localForm.value.status = newVal ? 'a' : 'b'
})

// Watch props changes
watch(() => props.form, (newVal) => {
  localForm.value = { ...newVal }
}, { deep: true })

watch(() => props.isActive, (newVal) => {
  localIsActive.value = newVal
})

const handleSubmit = () => {
  emit('submit')
}

const handleInvalid = () => {
  emit('invalid')
}

const handleCancel = () => {
  router.visit('/projects/types')
}
</script>


