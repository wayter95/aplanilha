import { useForm, defineField } from 'vee-validate'
import * as yup from 'yup'

export function useUserValidation() {
    const createUserSchema = yup.object({
        first_name: yup.string().required('O primeiro nome é obrigatório').min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, 'O primeiro nome deve conter apenas letras e espaços'),
        last_name: yup.string().required('O sobrenome é obrigatório').min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, 'O sobrenome deve conter apenas letras e espaços'),
        email: yup.string().required('O email é obrigatório').email().max(100),
        password: yup.string().required('A senha é obrigatória').min(8).max(50)
            .matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, 'A senha deve conter pelo menos 1 letra minúscula, 1 maiúscula e 1 número'),
        password_confirmation: yup.string().required('A confirmação de senha é obrigatória')
            .oneOf([yup.ref('password')], 'A confirmação de senha deve ser igual à senha'),
        role_id: yup.string().required('O perfil é obrigatório').uuid(),
        is_active: yup.boolean().default(true),
        phone: yup.string().nullable().matches(/^\(\d{2}\)\s\d{4,5}-\d{4}$/, 'Telefone inválido'),
        birth_date: yup.date().nullable().max(new Date(), 'A data de nascimento não pode ser futura')
    })

    function createUserForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: createUserSchema,
            initialValues: {
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role_id: '',
                is_active: true,
                phone: '',
                birth_date: '',
                ...initialValues
            }
        })

        const [firstName, firstNameAttrs] = defineField('first_name')
        const [lastName, lastNameAttrs] = defineField('last_name')
        const [email, emailAttrs] = defineField('email')
        const [password, passwordAttrs] = defineField('password')
        const [passwordConfirmation, passwordConfirmationAttrs] = defineField('password_confirmation')
        const [roleId, roleIdAttrs] = defineField('role_id')
        const [isActive, isActiveAttrs] = defineField('is_active')
        const [phone, phoneAttrs] = defineField('phone')
        const [birthDate, birthDateAttrs] = defineField('birth_date')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: {
                firstName, lastName, email, password, passwordConfirmation,
                roleId, isActive, phone, birthDate
            },
            attrs: {
                firstName: firstNameAttrs,
                lastName: lastNameAttrs,
                email: emailAttrs,
                password: passwordAttrs,
                passwordConfirmation: passwordConfirmationAttrs,
                roleId: roleIdAttrs,
                isActive: isActiveAttrs,
                phone: phoneAttrs,
                birthDate: birthDateAttrs
            }
        }
    }

    return {
        createUserSchema,
        createUserForm
    }
}
