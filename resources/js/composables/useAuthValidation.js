import { useForm, defineField } from 'vee-validate'
import * as yup from 'yup'

export function useAuthValidation() {
    const loginSchema = yup.object({
        email: yup.string().required('Email é obrigatório').email('Email inválido'),
        password: yup.string().required('Senha é obrigatória')
    })

    const resetPasswordSchema = yup.object({
        token: yup.string().required('Token é obrigatório'),
        email: yup.string().required('Email é obrigatório').email('Email inválido'),
        password: yup.string().required('Senha é obrigatória').min(8, 'A senha deve ter no mínimo 8 caracteres'),
        password_confirmation: yup.string()
            .required('Confirmação de senha é obrigatória')
            .oneOf([yup.ref('password')], 'A confirmação de senha deve ser igual à senha')
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

        const [email, emailAttrs] = defineField('email')
        const [password, passwordAttrs] = defineField('password')

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

        const [token, tokenAttrs] = defineField('token')
        const [email, emailAttrs] = defineField('email')
        const [password, passwordAttrs] = defineField('password')
        const [passwordConfirmation, passwordConfirmationAttrs] = defineField('password_confirmation')

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
