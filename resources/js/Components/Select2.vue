<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="selectId" 
      class="form-label text-defaulttextcolor"
    >
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    
    <select 
      :id="selectId"
      :name="name"
      :required="required"
      :disabled="disabled"
      :multiple="multiple"
      :class="selectClasses"
      ref="selectElement"
    >
      <option v-if="placeholder && !multiple" value="" disabled>{{ placeholder }}</option>
      <option 
        v-for="option in options" 
        :key="option[valueKey]" 
        :value="option[valueKey]"
      >
        {{ option[labelKey] }}
      </option>
    </select>
    
    <!-- Prioriza erro de validação do VeeValidate, depois o erro customizado -->
    <p v-if="displayError" class="text-danger text-xs mt-1">{{ displayError }}</p>
    <p v-if="help && !displayError" class="text-textmuted text-xs mt-1">{{ help }}</p>
  </div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { computed, onMounted, onBeforeUnmount, watch, ref, nextTick } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: null
  },
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Selecione uma opção'
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  multiple: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  help: {
    type: String,
    default: ''
  },
  // Opções estáticas
  options: {
    type: Array,
    default: () => []
  },
  // Opções dinâmicas via API
  apiUrl: {
    type: String,
    default: ''
  },
  // Parâmetros para a API
  apiParams: {
    type: Object,
    default: () => ({})
  },
  // Chave do valor no objeto de opção
  valueKey: {
    type: String,
    default: 'id'
  },
  // Chave do label no objeto de opção
  labelKey: {
    type: String,
    default: 'name'
  },
  // Permitir limpeza
  allowClear: {
    type: Boolean,
    default: true
  },
  // Busca no servidor
  ajax: {
    type: Boolean,
    default: false
  },
  // Delay para busca (ms)
  ajaxDelay: {
    type: Number,
    default: 250
  },
  // Mínimo de caracteres para buscar
  minimumInputLength: {
    type: Number,
    default: 0
  },
  // Máximo de seleções (para multiple)
  maximumSelectionLength: {
    type: Number,
    default: 0
  },
  // Props para integração com VeeValidate
  rules: {
    type: [String, Function, Object, Array],
    default: ''
  },
  validateOnBlur: {
    type: Boolean,
    default: true
  },
  validateOnInput: {
    type: Boolean,
    default: false
  },
  // Template customizado
  templateResult: {
    type: Function,
    default: null
  },
  templateSelection: {
    type: Function,
    default: null
  },
  // Classes CSS adicionais
  customClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'change', 'select', 'unselect', 'clear'])

const selectElement = ref(null)
const select2Instance = ref(null)
const loadedOptions = ref([...props.options])

// Usar VeeValidate se houver um nome de campo definido
const fieldName = computed(() => props.name || props.id)

// Configurar VeeValidate field apenas se houver rules
let field = null
let fieldValue = computed(() => props.modelValue)
let errorMessage = computed(() => '')
let meta = computed(() => ({ touched: false, valid: true }))

try {
  if (props.rules || fieldName.value) {
    const veeField = useField(fieldName.value, props.rules, {
      validateOnValueUpdate: props.validateOnInput,
      initialValue: props.modelValue
    })
    
    field = veeField
    fieldValue = veeField.value
    errorMessage = veeField.errorMessage
    meta = veeField.meta
  }
} catch (error) {
  console.debug('VeeValidate não disponível para este campo:', fieldName.value)
}

const selectId = computed(() => props.id)

const displayError = computed(() => {
  return errorMessage.value || props.error
})

const selectClasses = computed(() => {
  let classes = ['form-control']
  
  if (displayError.value) {
    classes.push('is-invalid')
  }
  
  if (props.customClass) {
    classes.push(props.customClass)
  }
  
  return classes.join(' ')
})

// Carregar opções da API
const loadOptionsFromApi = async () => {
  if (!props.apiUrl) return
  
  try {
    const response = await axios.get(props.apiUrl, {
      params: props.apiParams
    })
    
    // Assume que a resposta tem um array de dados
    loadedOptions.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Erro ao carregar opções do Select2:', error)
    loadedOptions.value = []
  }
}

// Configuração do Select2
const initSelect2 = async () => {
  if (!selectElement.value) return
  
  // Importar jQuery dinamicamente se necessário
  if (typeof window.$ === 'undefined') {
    console.error('jQuery não está disponível. Select2 requer jQuery.')
    return
  }
  
  // Importar Select2 se necessário
  if (typeof window.$.fn.select2 === 'undefined') {
    try {
      await import('select2')
      // Importar CSS do Select2
      const link = document.createElement('link')
      link.rel = 'stylesheet'
      link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
      document.head.appendChild(link)
    } catch (error) {
      console.error('Erro ao carregar Select2:', error)
      return
    }
  }
  
  const $select = window.$(selectElement.value)
  
  const config = {
    placeholder: props.placeholder,
    allowClear: props.allowClear,
    dir: document.documentElement.dir || 'ltr',
    width: '100%'
  }
  
  // Configuração para múltipla seleção
  if (props.multiple && props.maximumSelectionLength > 0) {
    config.maximumSelectionLength = props.maximumSelectionLength
  }
  
  // Configuração para busca Ajax
  if (props.ajax && props.apiUrl) {
    config.ajax = {
      url: props.apiUrl,
      dataType: 'json',
      delay: props.ajaxDelay,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          ...props.apiParams
        }
      },
      processResults: function (data, params) {
        params.page = params.page || 1
        
        const results = (data.data || data).map(item => ({
          id: item[props.valueKey],
          text: item[props.labelKey],
          ...item
        }))
        
        return {
          results: results,
          pagination: {
            more: data.next_page_url !== null
          }
        }
      },
      cache: true
    }
    config.minimumInputLength = props.minimumInputLength
  }
  
  // Templates customizados
  if (props.templateResult) {
    config.templateResult = props.templateResult
  }
  
  if (props.templateSelection) {
    config.templateSelection = props.templateSelection
  }
  
  // Inicializar Select2
  $select.select2(config)
  select2Instance.value = $select
  
  // Definir valor inicial
  if (props.modelValue !== null && props.modelValue !== undefined) {
    $select.val(props.modelValue).trigger('change.select2')
  }
  
  // Eventos
  $select.on('select2:select', (e) => {
    const value = props.multiple ? $select.val() : e.params.data.id
    updateValue(value)
    emit('select', e.params.data)
  })
  
  $select.on('select2:unselect', (e) => {
    const value = props.multiple ? $select.val() : null
    updateValue(value)
    emit('unselect', e.params.data)
  })
  
  $select.on('select2:clear', () => {
    updateValue(props.multiple ? [] : null)
    emit('clear')
  })
  
  $select.on('change', (e) => {
    const value = $select.val()
    updateValue(value)
  })
}

// Atualizar valor
const updateValue = (value) => {
  // Atualizar VeeValidate se disponível
  if (field) {
    fieldValue.value = value
  }
  
  // Emitir para v-model
  emit('update:modelValue', value)
  emit('change', value)
}

// Destruir Select2
const destroySelect2 = () => {
  if (select2Instance.value) {
    select2Instance.value.select2('destroy')
    select2Instance.value = null
  }
}

// Watch para mudanças no modelValue
watch(() => props.modelValue, (newValue) => {
  if (select2Instance.value && selectElement.value) {
    const currentValue = window.$(selectElement.value).val()
    
    // Evitar loop infinito comparando valores
    const isDifferent = props.multiple 
      ? JSON.stringify(currentValue) !== JSON.stringify(newValue)
      : currentValue != newValue
    
    if (isDifferent) {
      window.$(selectElement.value).val(newValue).trigger('change.select2')
    }
  }
})

// Watch para mudanças nas opções
watch(() => props.options, (newOptions) => {
  loadedOptions.value = [...newOptions]
  
  nextTick(() => {
    if (select2Instance.value) {
      destroySelect2()
      initSelect2()
    }
  })
})

// Watch para disabled
watch(() => props.disabled, (newDisabled) => {
  if (select2Instance.value) {
    window.$(selectElement.value).prop('disabled', newDisabled)
  }
})

// Lifecycle hooks
onMounted(async () => {
  if (props.apiUrl && !props.ajax) {
    await loadOptionsFromApi()
  }
  
  await nextTick()
  await initSelect2()
})

onBeforeUnmount(() => {
  destroySelect2()
})

// Expor métodos públicos
defineExpose({
  reload: async () => {
    await loadOptionsFromApi()
    destroySelect2()
    await nextTick()
    await initSelect2()
  },
  clear: () => {
    if (select2Instance.value) {
      select2Instance.value.val(null).trigger('change')
    }
  }
})
</script>

<style scoped>
/* Estilos adicionais podem ser adicionados aqui se necessário */
</style>
