<!--
    FormTextarea - Componente de textarea com validação visual
-->

<template>
    <div class="space-y-2">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="textareaId" 
            class="ti-form-label"
        >
            {{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
        </label>

        <!-- Textarea -->
        <textarea
            :id="textareaId"
            :name="name"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :rows="rows"
            :class="textareaClasses"
            :value="value"
            @input="handleInput"
            @blur="handleBlur"
            @focus="handleFocus"
        ></textarea>

        <!-- Error Message -->
        <p v-if="errorMessage" class="text-sm text-red-600 mt-2">
            {{ errorMessage }}
        </p>

        <!-- Success Message -->
        <p v-else-if="successMessage" class="text-sm text-green-600 mt-2">
            {{ successMessage }}
        </p>

        <!-- Help Text -->
        <p v-else-if="helpText" class="text-sm text-gray-600 dark:text-white/70 mt-2">
            {{ helpText }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useField } from 'vee-validate'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 3
    },
    rules: {
        type: [String, Function, Object],
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    errorMessage: {
        type: String,
        default: ''
    },
    successMessage: {
        type: String,
        default: ''
    },
    helpText: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showValidation: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input'])

const { value, errorMessage: veeError, handleBlur: veeBlur, meta } = useField(
    props.name,
    props.rules,
    {
        validateOnValueUpdate: false,
        initialValue: props.modelValue
    }
)

const textareaId = computed(() => `textarea-${props.name}-${Math.random().toString(36).substr(2, 9)}`)

const hasError = computed(() => {
    return props.showValidation && (!!props.errorMessage || (meta.touched && !!veeError.value))
})

const hasSuccess = computed(() => {
    return props.showValidation && !!props.successMessage && !hasError.value
})

const textareaClasses = computed(() => {
    const classes = ['ti-form-input']
    
    if (props.size === 'sm') {
        classes.push('!py-1.5', '!text-[0.75rem]')
    } else if (props.size === 'lg') {
        classes.push('!py-2.5', '!text-base')
    }
    
    if (hasError.value) {
        classes.push('!border-danger', 'focus:border-danger', 'focus:ring-danger')
    } else if (hasSuccess.value) {
        classes.push('!border-success', 'focus:border-success', 'focus:ring-success')
    }
    
    return classes.join(' ')
})

const handleInput = (event) => {
    const newValue = event.target.value
    value.value = newValue
    emit('update:modelValue', newValue)
    emit('input', event)
}

const handleBlur = (event) => {
    veeBlur()
    emit('blur', event)
}

const handleFocus = (event) => {
    emit('focus', event)
}

defineExpose({
    value,
    errorMessage: veeError,
    meta
})
</script>
