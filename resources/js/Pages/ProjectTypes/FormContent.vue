<template>
  <Form @submit="handleSubmit" @invalid="handleInvalid" :initial-values="form" :key="formKey" v-slot="{ errors }">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box">
          <div class="box-header">
            <div class="box-title">
              {{ mode === 'edit' ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto' }}
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
                <label for="color" class="form-label">
                  Cor <span class="text-danger">*</span>
                </label>
                <div class="flex items-center gap-3">
                  <input
                    id="color-picker"
                    type="color"
                    v-model="localForm.color"
                    class="form-control form-control-color !w-20"
                    title="Escolha a cor"
                  />
                  <Field
                    id="color"
                    name="color"
                    v-model="localForm.color"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.color }"
                    placeholder="#000000"
                    rules="required"
                  />
                </div>
                <ErrorMessage name="color" class="invalid-feedback" />
              </div>

              <!-- Status -->
              <div class="xl:col-span-6 col-span-12">
                <label class="form-label">Status</label>
                <div class="flex items-center gap-3 mt-2">
                  <div class="toggle on mb-0">
                    <span></span>
                    <input
                      id="status-toggle"
                      type="checkbox"
                      v-model="localIsActive"
                      class="toggle-checkbox"
                    />
                  </div>
                  <label for="status-toggle" class="text-[0.875rem] text-defaulttextcolor mb-0 cursor-pointer">
                    {{ localIsActive ? 'Ativo' : 'Bloqueado' }}
                  </label>
                </div>
                <div class="text-[0.75rem] text-textmuted mt-1">
                  {{ localIsActive ? 'Este tipo de projeto está disponível para uso' : 'Este tipo de projeto está bloqueado' }}
                </div>
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

<style scoped>
.toggle {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle span {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 34px;
}

.toggle span:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

.toggle input:checked + span {
  background-color: rgb(var(--primary-rgb));
}

.toggle input:checked + span:before {
  transform: translateX(20px);
}

.form-control-color {
  height: 40px;
  cursor: pointer;
}

.invalid-feedback {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: rgb(var(--danger-rgb));
}

.is-invalid {
  border-color: rgb(var(--danger-rgb));
}
</style>
