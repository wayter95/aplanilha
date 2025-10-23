// composables/useUserValidation.js
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useUserValidation() {
    // 📌 Schema para criação de usuário
    const createUserSchema = yup.object({
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.name))
            .min(2, ptBR.messages.min.replace('{field}', ptBR.names.name).replace('{length}', '2'))
            .max(100, ptBR.messages.max.replace('{field}', ptBR.names.name).replace('{length}', '100'))
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.name)),

        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email)),

        password: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.password))
            .min(8, ptBR.messages.min.replace('{field}', ptBR.names.password).replace('{length}', '8')),

        role: yup.string()
            .required('Função obrigatória.'),

        status: yup.string()
            .required('Status obrigatório.')
    })

    // 📌 Schema para atualização de usuário
    const updateUserSchema = yup.object({
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.name))
            .min(2, ptBR.messages.min.replace('{field}', ptBR.names.name).replace('{length}', '2'))
            .max(100, ptBR.messages.max.replace('{field}', ptBR.names.name).replace('{length}', '100'))
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.name)),

        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email)),

        // Senha não obrigatória na atualização
        password: yup.string()
            .nullable()
            .transform((value, originalValue) => originalValue === '' ? null : value)
            .min(8, ptBR.messages.min.replace('{field}', ptBR.names.password).replace('{length}', '8'))
            .notRequired(),

        role: yup.string()
            .required('Função obrigatória.'),

        status: yup.string()
            .required('Status obrigatório.')
    })

    // 📌 Função para criar o formulário de criação
    function createUserForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: createUserSchema,
            initialValues: {
                name: '',
                email: '',
                password: '',
                role: '',
                status: '',
                ...initialValues
            }
        })

    const { value: name, errorMessage: nameError, handleBlur: nameBlur, handleChange: nameChange } = useField('name')
    const { value: email, errorMessage: emailError, handleBlur: emailBlur, handleChange: emailChange } = useField('email')
    const { value: password, errorMessage: passwordError, handleBlur: passwordBlur, handleChange: passwordChange } = useField('password')
    const { value: role, errorMessage: roleError, handleBlur: roleBlur, handleChange: roleChange } = useField('role')
    const { value: status, errorMessage: statusError, handleBlur: statusBlur, handleChange: statusChange } = useField('status')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, email, password, role, status },
            attrs: {
                name: { errorMessage: nameError, onBlur: nameBlur, onChange: nameChange },
                email: { errorMessage: emailError, onBlur: emailBlur, onChange: emailChange },
                password: { errorMessage: passwordError, onBlur: passwordBlur, onChange: passwordChange },
                role: { errorMessage: roleError, onBlur: roleBlur, onChange: roleChange },
                status: { errorMessage: statusError, onBlur: statusBlur, onChange: statusChange }
            }
        }
    }

    // 📌 Função para criar o formulário de atualização
    function updateUserForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: updateUserSchema,
            initialValues: {
                name: '',
                email: '',
                password: '',
                role: '',
                status: '',
                ...initialValues
            }
        })

    const { value: name, errorMessage: nameError, handleBlur: nameBlur, handleChange: nameChange } = useField('name')
    const { value: email, errorMessage: emailError, handleBlur: emailBlur, handleChange: emailChange } = useField('email')
    const { value: password, errorMessage: passwordError, handleBlur: passwordBlur, handleChange: passwordChange } = useField('password')
    const { value: role, errorMessage: roleError, handleBlur: roleBlur, handleChange: roleChange } = useField('role')
    const { value: status, errorMessage: statusError, handleBlur: statusBlur, handleChange: statusChange } = useField('status')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, email, password, role, status },
            attrs: {
                name: { errorMessage: nameError, onBlur: nameBlur, onChange: nameChange },
                email: { errorMessage: emailError, onBlur: emailBlur, onChange: emailChange },
                password: { errorMessage: passwordError, onBlur: passwordBlur, onChange: passwordChange },
                role: { errorMessage: roleError, onBlur: roleBlur, onChange: roleChange },
                status: { errorMessage: statusError, onBlur: statusBlur, onChange: statusChange }
            }
        }
    }

    return {
        createUserSchema,
        updateUserSchema,
        createUserForm,
        updateUserForm
    }
}
