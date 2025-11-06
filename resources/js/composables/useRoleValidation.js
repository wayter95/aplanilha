import { useForm, defineField } from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useRoleValidation() {
    const createRoleSchema = yup.object({
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Role name'))
            .min(2)
            .max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', 'Role name')),
        description: yup.string().nullable().max(255),
        client_id: yup.string().required(ptBR.messages.required.replace('{field}', 'Client')).uuid(),
        is_active: yup.boolean().default(true)
    })

    const updateRoleSchema = yup.object({
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Role name'))
            .min(2)
            .max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', 'Role name')),
        description: yup.string().nullable().max(255),
        is_active: yup.boolean().default(true)
    })

    function createRoleForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: createRoleSchema,
            initialValues: {
                name: '',
                description: '',
                client_id: '',
                is_active: true,
                ...initialValues
            }
        })
        const [name, nameAttrs] = defineField('name')
        const [description, descriptionAttrs] = defineField('description')
        const [clientId, clientIdAttrs] = defineField('client_id')
        const [isActive, isActiveAttrs] = defineField('is_active')
        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, description, clientId, isActive },
            attrs: { name: nameAttrs, description: descriptionAttrs, clientId: clientIdAttrs, isActive: isActiveAttrs }
        }
    }

    function updateRoleForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: updateRoleSchema,
            initialValues: {
                name: '',
                description: '',
                is_active: true,
                ...initialValues
            }
        })
        const [name, nameAttrs] = defineField('name')
        const [description, descriptionAttrs] = defineField('description')
        const [isActive, isActiveAttrs] = defineField('is_active')
        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, description, isActive },
            attrs: { name: nameAttrs, description: descriptionAttrs, isActive: isActiveAttrs }
        }
    }

    return {
        createRoleSchema,
        updateRoleSchema,
        createRoleForm,
        updateRoleForm
    }
}
