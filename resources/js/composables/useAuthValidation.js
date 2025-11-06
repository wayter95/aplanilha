import { useForm, defineRule} from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useAuthValidation() {
    const loginSchema = yup.object({
        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email)),
        password: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.password))
    })

    const resetPasswordSchema = yup.object({
        token: yup.string().required(ptBR.messages.required.replace('{field}', 'Token')),
        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email)),
        password: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.password))
            .min(8, ptBR.messages.min.replace('{field}', ptBR.names.password).replace('{length}', '8')),
        password_confirmation: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.password_confirmation))
            .oneOf([yup.ref('password')], ptBR.messages.confirmed.replace('{field}', ptBR.names.password))
    })

    function createLoginForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: loginSchema,
            initialValues: {
                email: '',
                password: '',
                remember: false,
                ...initialValues
            }
        })

        const [email, emailAttrs] = defineRule('email')
        const [password, passwordAttrs] = defineRule('password')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { email, password },
            attrs: { email: emailAttrs, password: passwordAttrs }
        }
    }

    function createResetPasswordForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: resetPasswordSchema,
            initialValues: {
                token: '',
                email: '',
                password: '',
                password_confirmation: '',
                ...initialValues
            }
        })

        const [token, tokenAttrs] = defineRule('token')
        const [email, emailAttrs] = defineRule('email')
        const [password, passwordAttrs] = defineRule('password')
        const [passwordConfirmation, passwordConfirmationAttrs] = defineRule('password_confirmation')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { token, email, password, passwordConfirmation },
            attrs: { token: tokenAttrs, email: emailAttrs, password: passwordAttrs, passwordConfirmation: passwordConfirmationAttrs }
        }
    }

    return {
        loginSchema,
        resetPasswordSchema,
        createLoginForm,
        createResetPasswordForm
    }
}
