// composables/useRoleValidation.js
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useRoleValidation() {
    // 📌 Schema para criação de função
    const createRoleSchema = yup.object({
        name: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Nome (identificador único)'))
            .min(3, ptBR.messages.min.replace('{field}', 'Nome (identificador único)').replace('{length}', '3'))
            .max(50, ptBR.messages.max.replace('{field}', 'Nome (identificador único)').replace('{length}', '50')),
        display_name: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Nome de Exibição'))
            .min(3, ptBR.messages.min.replace('{field}', 'Nome de Exibição').replace('{length}', '3'))
            .max(100, ptBR.messages.max.replace('{field}', 'Nome de Exibição').replace('{length}', '100')),
        description: yup
            .string()
            .nullable()
            .max(500, ptBR.messages.max.replace('{field}', 'Descrição').replace('{length}', '500')),
        status: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Status')),
        permissions: yup
            .array()
            .of(yup.number().integer())
            .nullable()
    })

    // 📌 Schema para atualização de função
    const updateRoleSchema = yup.object({
        name: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Nome (identificador único)'))
            .min(3, ptBR.messages.min.replace('{field}', 'Nome (identificador único)').replace('{length}', '3'))
            .max(50, ptBR.messages.max.replace('{field}', 'Nome (identificador único)').replace('{length}', '50')),
        display_name: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Nome de Exibição'))
            .min(3, ptBR.messages.min.replace('{field}', 'Nome de Exibição').replace('{length}', '3'))
            .max(100, ptBR.messages.max.replace('{field}', 'Nome de Exibição').replace('{length}', '100')),
        description: yup
            .string()
            .nullable()
            .max(500, ptBR.messages.max.replace('{field}', 'Descrição').replace('{length}', '500')),
        status: yup
            .string()
            .required(ptBR.messages.required.replace('{field}', 'Status')),
        permissions: yup
            .array()
            .of(yup.number().integer())
            .nullable()
    })

    // 🧭 Função auxiliar para criar formulário de criação
    function createRoleForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: createRoleSchema,
            initialValues: {
                name: '',
                display_name: '',
                description: '',
                status: '',
                permissions: [],
                ...initialValues
            }
        })

    const { value: name, errorMessage: nameError, handleBlur: nameBlur, handleChange: nameChange } = useField('name')
    const { value: display_name, errorMessage: displayNameError, handleBlur: displayNameBlur, handleChange: displayNameChange } = useField('display_name')
    const { value: description, errorMessage: descriptionError, handleBlur: descriptionBlur, handleChange: descriptionChange } = useField('description')
    const { value: status, errorMessage: statusError, handleBlur: statusBlur, handleChange: statusChange } = useField('status')
    const { value: permissions, errorMessage: permissionsError, handleBlur: permissionsBlur, handleChange: permissionsChange } = useField('permissions')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, display_name, description, status, permissions },
            attrs: {
                name: { errorMessage: nameError, onBlur: nameBlur, onChange: nameChange },
                display_name: { errorMessage: displayNameError, onBlur: displayNameBlur, onChange: displayNameChange },
                description: { errorMessage: descriptionError, onBlur: descriptionBlur, onChange: descriptionChange },
                status: { errorMessage: statusError, onBlur: statusBlur, onChange: statusChange },
                permissions: { errorMessage: permissionsError, onBlur: permissionsBlur, onChange: permissionsChange }
            }
        }
    }

    // 🧭 Função auxiliar para criar formulário de atualização
    function updateRoleForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: updateRoleSchema,
            initialValues: {
                name: '',
                display_name: '',
                description: '',
                status: '',
                permissions: [],
                ...initialValues
            }
        })

    const { value: name, errorMessage: nameError, handleBlur: nameBlur, handleChange: nameChange } = useField('name')
    const { value: display_name, errorMessage: displayNameError, handleBlur: displayNameBlur, handleChange: displayNameChange } = useField('display_name')
    const { value: description, errorMessage: descriptionError, handleBlur: descriptionBlur, handleChange: descriptionChange } = useField('description')
    const { value: status, errorMessage: statusError, handleBlur: statusBlur, handleChange: statusChange } = useField('status')
    const { value: permissions, errorMessage: permissionsError, handleBlur: permissionsBlur, handleChange: permissionsChange } = useField('permissions')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, display_name, description, status, permissions },
            attrs: {
                name: { errorMessage: nameError, onBlur: nameBlur, onChange: nameChange },
                display_name: { errorMessage: displayNameError, onBlur: displayNameBlur, onChange: displayNameChange },
                description: { errorMessage: descriptionError, onBlur: descriptionBlur, onChange: descriptionChange },
                status: { errorMessage: statusError, onBlur: statusBlur, onChange: statusChange },
                permissions: { errorMessage: permissionsError, onBlur: permissionsBlur, onChange: permissionsChange }
            }
        }
    }

    return {
        createRoleSchema,
        updateRoleSchema,
        createRoleForm,
        updateRoleForm
    }
}
